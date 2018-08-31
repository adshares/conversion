<?php

namespace Adshares\Ads\Scanner;


use Adshares\Ads\AdsClient;
use Adshares\Ads\Driver\CliDriver;
use Illuminate\Database\DatabaseManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class AdsScanner implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var \Adshares\Ads\AdsClient
     */
    private $ads;

    /**
     * @var DatabaseManager
     */
    private $db;

    /**
     * @var int
     */
    private $startBlock;

    /**
     * AdsScanner constructor.
     * @param string $host
     * @param int $port
     * @param string $address
     * @param string $secret
     * @param DatabaseManager $db
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        string $host,
        int $port,
        string $address,
        string $secret,
        DatabaseManager $db,
        LoggerInterface $logger = null
    ) {
        if (null === $logger) {
            $logger = new NullLogger();
        }

        $this->ads = new AdsClient(new CliDriver(
            $address,
            $secret,
            $host,
            $port,
            $logger
        ), $logger);

        $this->db = $db;
        $this->logger = $logger;
    }


    /**
     * @param int $startBlock
     */
    public function setStartBlock(int $startBlock): void
    {
        $this->startBlock = $startBlock;
    }

    /**
     * @return int
     */
    private function getBlockNumber(): int
    {
        $result = $this->db->select('SELECT MAX(block_number) AS block_number FROM ads_scans');
        $result = array_pop($result);

        return null !== $result->block_number ? (int)$result->block_number + 512 : $this->startBlock;
    }

    /**
     * @param int $blockNumber
     * @return bool
     */
    private function saveBlockNumber(int $blockNumber): bool
    {
        return $this->db->insert(
            'INSERT INTO ads_scans ( block_number ) VALUES (?)',
            [$blockNumber]
        );
    }

    /**
     * @return int
     */
    public function scan(): int
    {
        $blockNumber = $this->getBlockNumber();
        $this->logger->info(sprintf('Scanning from block %s...', dechex($blockNumber)));

        $messageIds = $this->ads->getMessageIds(dechex($blockNumber));

//        var_dump($messageIds);


//        $logs = $this->getLogs($blockNumber);
//        $this->logger->info(sprintf('Found %d logs', count($logs)));
//        if (empty($logs)) {
//            return 0;
//        }
//
        $count = 0;
//        $this->db->transaction(function () use ($blockNumber, $logs, &$count) {
//
//            foreach ($logs as $log) {
//                $this->logger->debug(sprintf('Converting %s', $log->transactionHash));
//                $blockNumber = max($blockNumber, hexdec($log->blockNumber));
//
//                if (null === ($transaction = $this->getTransaction($log->transactionHash))) {
//                    throw new \RuntimeException(sprintf('Cannot fetch transaction %s', $log->transactionHash));
//                }
//
//                $transaction->status = $this->extractConversionData($transaction);
//                if ($transaction->status < 0) {
//                    $this->logger->warning(sprintf('Cannot convert transaction %s', $log->transactionHash));
//                    continue;
//                }
//
//                $transaction->timestamp = $this->getBlockTimestamp($log->blockHash);
//
//                if (!$this->saveTransaction($transaction)) {
//                    throw new \RuntimeException(sprintf('Cannot save transaction %s', $log->transactionHash));
//                }
//
//                ++$count;
//            }
//
//            if (!$this->saveBlockNumber($blockNumber)) {
//                throw new \RuntimeException('Cannot log scan');
//            }
//
//            $this->logger->info(sprintf('Scanned to block %d', $blockNumber));
//        });

        return $count;
    }
}