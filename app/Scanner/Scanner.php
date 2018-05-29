<?php

namespace Adshares\Ads\Scanner;

use Illuminate\Database\DatabaseManager;
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
     * @var DatabaseManager
     */
    private $db;

    /**
     * @var int
     */
    private $startBlock;

    /**
     * @var string
     */
    private $transferTopic;

    /**
     * @var string
     */
    private $transferMethod;

    /**
     * @var string
     */
    private $contractAddress;

    /**
     * @var string
     */
    private $burnAddress;

    /**
     * @var array
     */
    private $blockCache = [];

    /**
     * Scanner constructor.
     * @param string $url
     * @param DatabaseManager $db
     */
    public function __construct(string $url, DatabaseManager $db)
    {
        $this->web3 = new Web3($url);
        $this->db = $db;
    }


    /**
     * @param int $startBlock
     */
    public function setStartBlock(int $startBlock): void
    {
        $this->startBlock = $startBlock;
    }

    /**
     * @param string $transferTopic
     */
    public function setTransferTopic(string $transferTopic): void
    {
        $this->transferTopic = $transferTopic;
    }

    /**
     * @param string $transferMethod
     */
    public function setTransferMethod(string $transferMethod): void
    {
        $this->transferMethod = $transferMethod;
    }

    /**
     * @param string $contractAddress
     */
    public function setContractAddress(string $contractAddress): void
    {
        $this->contractAddress = $contractAddress;
    }

    /**
     * @param string $burnAddress
     */
    public function setBurnAddress(string $burnAddress): void
    {
        $this->burnAddress = $burnAddress;
    }

    /**
     * @param string $hex
     * @return string
     */
    public static function sanitizeHex(string $hex): string
    {
        $result = strtolower($hex);
        $result = preg_replace('/^0x/', '', $result);
        $result = preg_replace('/^0+/', '', $result);

        return $result;
    }

    /**
     * @return int
     */
    private function getBlockNumber(): int
    {
        $result = $this->db->select('SELECT MAX(block_number) AS block_number FROM scans');
        $result = array_pop($result);

        return null !== $result->block_number ? (int)$result->block_number + 1 : $this->startBlock;
    }

    /**
     * @param int $blockNumber
     * @return bool
     */
    private function saveBlockNumber(int $blockNumber): bool
    {
        return $this->db->insert(
            'INSERT INTO scans ( block_number ) VALUES (?)',
            [$blockNumber]
        );
    }


    /**
     * @param int $retry
     * @param int $blockNumber
     * @return string|null
     */
    private function createFilter(int $blockNumber, int $retry = 2): ?string
    {
        $id = null;

        $this->web3->eth->newFilter([
            'fromBlock' => '0x' . dechex($blockNumber),
            'topics' => [$this->transferTopic],
            'address' => $this->contractAddress
        ], function ($err, $result) use (&$id) {

            if ($err !== null) {
                $this->logger->error(sprintf('Creating filter error: %s', $err->getMessage()));

                return;
            }

            $id = $result;
        });

        if (null === $id && $retry) {
            $id = $this->createFilter($blockNumber, --$retry);
        }

        return $id;
    }

    /**
     * @param int $retry
     * @param int $blockNumber
     * @return array
     */
    private function getLogs(int $blockNumber, int $retry = 2): array
    {
        $logs = null;

        if (null === ($filter = $this->createFilter($blockNumber))) {
            return [];
        }

        $this->web3->eth->getFilterLogs($filter, function ($err, $result) use (&$logs) {

            if ($err !== null) {
                $this->logger->error(sprintf('Fetching logs error: %s', $err->getMessage()));

                return;
            }

            $logs = $result;
        });

        if (null === $logs && $retry) {
            $logs = $this->getLogs($blockNumber, --$retry);
        }

        return null === $logs ? [] : $logs;
    }

    /**
     * @param string $hash
     * @param int $retry
     * @return \stdClass
     */
    private function getBlock(string $hash, int $retry = 2): ?\stdClass
    {
        if (!isset($this->blockCache[$hash])) {
            $this->web3->eth->getBlockByHash($hash, false, function ($err, $result) {

                if ($err !== null) {
                    $this->logger->error(sprintf('Fetching block error: %s', $err->getMessage()));

                    return;
                }

                $this->blockCache[$result->hash] = $result;
            });

            if (!isset($this->blockCache[$hash]) && $retry) {
                $this->blockCache[$hash] = $this->getBlock($hash, --$retry);
            }
        }

        return isset($this->blockCache[$hash]) ? $this->blockCache[$hash] : null;
    }

    /**
     * @param string $hash
     * @return int
     */
    private function getBlockTimestamp(string $hash): int
    {
        if (null === ($block = $this->getBlock($hash))) {
            $this->logger->error(sprintf('Cannot fetch block %s', $hash));

            return 0;
        }

        return (int)hexdec($block->timestamp);
    }

    /**
     * @param string $hash
     * @param int $retry
     * @return \stdClass
     */
    private function getTransaction(string $hash, int $retry = 2): ?\stdClass
    {
        $transaction = null;

        $this->web3->eth->getTransactionByHash($hash, function ($err, $result) use (&$transaction) {

            if ($err !== null) {
                $this->logger->error(sprintf('Fetching transaction error: %s', $err->getMessage()));

                return;
            }

            $transaction = $result;
        });

        if (null === $transaction && $retry) {
            $transaction = $this->getTransaction($hash, --$retry);
        }

        return $transaction;
    }

    /**
     * @param \stdClass $transaction
     * @return bool
     */
    private function extractConversionData(\stdClass $transaction): bool
    {
        if (self::sanitizeHex($transaction->to) !== self::sanitizeHex($this->contractAddress)) {

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
        if (self::sanitizeHex($transferMethod) !== self::sanitizeHex($this->transferMethod)) {
            $this->logger->debug(sprintf(
                'Incorrect transfer method; got %s, should be %s.',
                $transferMethod,
                $this->transferMethod
            ));

            return false;
        }

        $data = substr($input, strlen($this->transferMethod));
        $data = str_split($data, 64);

        if (3 !== count($data)) {
            $this->logger->debug(sprintf(
                'Incorrect number of parameters; got %d, should be 3.',
                count($data)
            ));

            return false;
        }

        if (self::sanitizeHex($data[0]) !== self::sanitizeHex($this->burnAddress)) {
            $this->logger->debug(sprintf(
                'Incorrect burn address; got %s, should be %s.',
                '0x' . $data[0],
                $this->burnAddress
            ));

            return false;
        }

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
    private function saveTransaction(\stdClass $transaction): bool
    {
        return $this->db->insert(
            'INSERT INTO transactions (
                from_address,
                log_date,
                amount,
                public_key
            ) VALUES (?, ?, ?, ?)', [
            $transaction->from,
            new \DateTime('@' . $transaction->timestamp),
            $transaction->burnAmount,
            $transaction->conversionKey
        ]);
    }

    /**
     * @return int
     */
    public function scan(): int
    {
        $blockNumber = $this->getBlockNumber();
        $this->logger->info(sprintf('Scanning from block %d...', $blockNumber));

        $logs = $this->getLogs($blockNumber);
        $this->logger->info(sprintf('Found %d logs', count($logs)));
        if (empty($logs)) {
            return 0;
        }

        $count = 0;
        $this->db->transaction(function () use ($blockNumber, $logs, &$count) {

            foreach ($logs as $log) {
                $this->logger->debug(sprintf('Converting %s', $log->transactionHash));
                $blockNumber = max($blockNumber, hexdec($log->blockNumber));

                if (null === ($transaction = $this->getTransaction($log->transactionHash))) {
                    throw new \RuntimeException(sprintf('Cannot fetch transaction %s', $log->transactionHash));
                }

                if (!$this->extractConversionData($transaction)) {
                    $this->logger->warning(sprintf('Cannot convert transaction %s', $log->transactionHash));
                    continue;
                }

                $transaction->timestamp = $this->getBlockTimestamp($log->blockHash);

                if (!$this->saveTransaction($transaction)) {
                    throw new \RuntimeException(sprintf('Cannot save transaction %s', $log->transactionHash));
                }

                ++$count;
            }

            if (!$this->saveBlockNumber($blockNumber)) {
                throw new \RuntimeException('Cannot log scan');
            }

            $this->logger->info(sprintf('Scanned to block %d', $blockNumber));
        });

        return $count;
    }
}
