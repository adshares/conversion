<?php

namespace Adshares\Ads\Scanner;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Web3\Web3;

class Scanner implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var \Web3\Web3
     */
    private $web3;

    /**
     * @var string
     */
    private $startBlock = '0x56BC12';

    /**
     * @var string
     */
    private $transferTopic = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';

    /**
     * @var string
     */
    private $transferMethod = '0xa9059cbb';

    /**
     * @var string
     */
    private $contractAddress = '0x422866a8f0b032c5cf1dfbdef31a20f4509562b0';

    /**
     * @var string
     */
    private $burnAddress = '0x0000000000000000000000000000000000000000000000000000000000000000';

    /**
     * @var array
     */
    private $blockCache = [];

    /**
     * Scanner constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->web3 = new Web3($url);
    }

    /**
     * @return int
     */
    private function createFilter()
    {
        $id = 0;

        $this->web3->eth->newFilter([
            'fromBlock' => $this->startBlock,
            'topics' => [$this->transferTopic],
            'address' => $this->contractAddress
        ], function ($err, $result) use (&$id) {

            if ($err !== null) {
                $this->logger->error(sprintf('Creating filter error: %s', $err->getMessage()));

                return;
            }

            $id = $result;
        });

        return $id;
    }

    /**
     * @param string $id
     * @return array
     */
    private function getLogs(string $id)
    {
        $logs = [];

        $this->web3->eth->getFilterLogs($id, function ($err, $result) use (&$logs) {

            if ($err !== null) {
                $this->logger->error(sprintf('Fetching logs error: %s', $err->getMessage()));

                return;
            }

            $logs = $result;
        });

        return $logs;
    }

    /**
     * @param string $hash
     * @return \stdClass
     */
    private function getBlock(string $hash)
    {
        if (!isset($this->blockCache[$hash])) {
            $this->web3->eth->getBlockByHash($hash, false, function ($err, $result) {

                if ($err !== null) {
                    $this->logger->error(sprintf('Fetching block error: %s', $err->getMessage()));

                    return;
                }

                $this->blockCache[$result->hash] = $result;
            });
        }

        return isset($this->blockCache[$hash]) ? $this->blockCache[$hash] : null;
    }

    /**
     * @param string $hash
     * @return int
     */
    private function getBlockTimestamp(string $hash)
    {
        if (null === ($block = $this->getBlock($hash))) {
            $this->logger->error(sprintf('Cannot fetch block %s', $hash));

            return 0;
        }

        return (int)hexdec($block->timestamp);
    }

    /**
     * @param string $hash
     * @return \stdClass
     */
    private function getTransaction(string $hash)
    {
        $transaction = null;

        $this->web3->eth->getTransactionByHash($hash, function ($err, $result) use (&$transaction) {

            if ($err !== null) {
                $this->logger->error(sprintf('Fetching transaction error: %s', $err->getMessage()));

                return;
            }

            $transaction = $result;
        });

        return $transaction;
    }

    /**
     * @param \stdClass $transaction
     * @return bool
     */
    private function extractConversionData(\stdClass $transaction)
    {
        if ($transaction->to !== $this->contractAddress) {

            $this->logger->debug(sprintf(
                'Incorrect contract address; got %s, should be %s',
                $transaction->to,
                $this->contractAddress
            ));

            return false;
        }

        $input = $transaction->input;
        $this->logger->debug(sprintf('Transaction data %s', $input));

        $transferMethod = substr($input, 0, strlen($this->transferMethod));
        if ($transferMethod !== $this->transferMethod) {
            $this->logger->debug(sprintf(
                'Incorrect transfer method; got %s, should be %s.',
                $transferMethod,
                $this->transferMethod
            ));

            return false;
        }

        $data = substr($input, strlen($this->transferMethod));
        $data = str_split($data, 64);

        // FIXME remove this mock
        $data[2] = $data[0];

        if (3 !== count($data)) {
            $this->logger->debug(sprintf(
                'Incorrect number of parameters; got %d, should be 3.',
                count($data)
            ));

            return false;
        }

        // FIXME uncomment this
//        if ('0x' . $data[0] !== $this->burnAddress) {
//            $this->logger->debug(sprintf(
//                'Incorrect burn address; got %s, should be %s.',
//                '0x' . $data[0],
//                $this->burnAddress
//            ));
//
//            return false;
//        }

        $burnAmountData = '0x' . preg_replace('/^0+/', '', $data[1]);
        if (1 > ($burnAmount = (int)hexdec($burnAmountData))) {
            $this->logger->debug(sprintf(
                'Incorrect burn amount; got %d [%s], should be more then 1.',
                $burnAmount,
                $burnAmountData
            ));

            return false;
        }

        if (64 !== strlen($data[2])) {
            $this->logger->debug(sprintf(
                'Incorrect conversion key; got %s.',
                $data[1]
            ));

            return false;
        }

        $transaction->burnAmount = $burnAmount;
        $transaction->conversionKey = '0x' . $data[2];

        return true;
    }

    /**
     * @param \stdClass $transaction
     * @return bool
     */
    private function saveTransaction(\stdClass $transaction)
    {
        dump($transaction->from);
        dump($transaction->to);
        dump($transaction->timestamp);
        dump($transaction->burnAmount);
        dump($transaction->conversionKey);

        return true;
    }

    /**
     * @return int
     */
    public function scan()
    {
        $eth = $this->web3->eth;
        $this->logger->debug('Scanning...');

        if (0 === ($filter = $this->createFilter())) {
            return 0;
        }
        $logs = $this->getLogs($filter);
        $this->logger->info(sprintf('Found %d logs', count($logs)));

        $count = 0;
        foreach ($logs as $log) {
            $this->logger->debug(sprintf('Converting %s', $log->transactionHash));

            if (null === ($transaction = $this->getTransaction($log->transactionHash))) {
                $this->logger->error(sprintf('Cannot fetch transaction %s', $log->transactionHash));
                continue;
            }

            if (!$this->extractConversionData($transaction)) {
                $this->logger->warning(sprintf('Cannot convert transaction %s', $log->transactionHash));
                continue;
            }

            $transaction->timestamp = $this->getBlockTimestamp($log->blockHash);

            if (!$this->saveTransaction($transaction)) {
                $this->logger->error(sprintf('Cannot save transaction %s', $log->transactionHash));
                continue;
            }

//            dump( new \DateTime('@' . $transaction->timestamp));
//            dump($transaction);

            ++$count;
        }

        return $count;
    }
}
