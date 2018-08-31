<?php

namespace Adshares\Ads\Scanner;


use Adshares\Ads\AdsClient;
use Adshares\Ads\Driver\CliDriver;
use Adshares\Ads\Entity\Transaction\SendOneTransaction;
use Illuminate\Database\DatabaseManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class AdsScanner implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const BLOCK_LENGTH = 512;

    /**
     * @var \Adshares\Ads\AdsClient
     */
    private $ads;

    /**
     * @var DatabaseManager
     */
    private $db;

    /**
     * @var string
     */
    private $treasuryAddress;

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
     * @param string $treasuryAddress
     */
    public function setTreasuryAddress(string $treasuryAddress): void
    {
        $this->treasuryAddress = $treasuryAddress;
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
     * @param SendOneTransaction $transaction
     * @return bool
     */
    private function saveTransaction(SendOneTransaction $transaction): bool
    {
        return $this->db->insert(
            'INSERT INTO ads_transactions (
                id,
                time,
                target_address,
                amount,
                message
            ) VALUES (?, ?, ?, ?, ?)', [
            $transaction->getId(),
            $transaction->getTime(),
            $transaction->getTargetAddress(),
            $transaction->getAmount(),
            $transaction->getMessage()
        ]);
    }

    /**
     * @return int
     */
    public function scan(): int
    {
        $blockNumber = $this->getBlockNumber();
        $this->logger->info(sprintf('Scanning from block %s...', dechex($blockNumber)));

        if ($blockNumber > time() - (self::BLOCK_LENGTH + 30)) {
            $this->logger->debug('Nothing to scan');
            return 0;
        }

        $nodeId = substr($this->treasuryAddress, 0, 4);
        $this->logger->info($nodeId);
        $count = 0;

        while (true) {
            foreach ($this->ads->getMessageIds(dechex($blockNumber))->getMessageIds() as $messageId) {
                if (0 !== strpos($messageId, $nodeId)) {
                    continue;
                }
                $this->logger->debug(sprintf('Scanning message %s...', $messageId));

                foreach ($this->ads->getMessage($messageId)->getTransactions() as $transaction) {

                    if (
                        !$transaction instanceof SendOneTransaction ||
                        $transaction->getSenderAddress() !== $this->treasuryAddress
                    ) {
                        continue;
                    }

                    if (!$this->saveTransaction($transaction)) {
                        throw new \RuntimeException(sprintf('Cannot save transaction %s', $transaction->getId()));
                    }

                    ++$count;
                }

            }

            if ($blockNumber > time() - 2 * self::BLOCK_LENGTH) {
                break;
            }

            $blockNumber += self::BLOCK_LENGTH;
        }

        if (!$this->saveBlockNumber($blockNumber)) {
            throw new \RuntimeException('Cannot log scan');
        }

        $this->logger->info(sprintf('Scanned to block %s', dechex($blockNumber)));

        return $count;
    }

    /**
     * @return int
     */
    public function pair(): int
    {
        $conversions = $this->db->select(
            'SELECT
              id,
              tx_hash,
              amount,
              ads_address
            FROM conversions
            WHERE status=0');

        $this->logger->info(sprintf('Found %d conversions', count($conversions)));

        $count = 0;
        foreach ($conversions as $conversion) {

            $conversion->amount = $conversion->amount * 1e11;

            $transaction = $this->db->selectOne(
                'SELECT
                          id,
                          target_address,
                          amount
                        FROM ads_transactions
                        WHERE message=?',
                [
                    preg_replace('/^0x/', '', $conversion->tx_hash)
                ]);

            if (null === $transaction) {
                $this->logger->debug(sprintf('No transaction yet for %s', $conversion->tx_hash));
                continue;
            }

            if ($conversion->ads_address != $transaction->target_address) {
                $status = 20;
                $info = sprintf(
                    'Mismatched transaction address; got %s, should be %s.',
                    $conversion->ads_address,
                    $transaction->target_address
                );
                $this->logger->debug($info);
            } elseif ($conversion->amount != $transaction->amount) {
                $status = 21;
                $info = sprintf(
                    'Mismatched transaction amount; got %d, should be %d.',
                    $conversion->amount,
                    $transaction->amount
                );
                $this->logger->debug($info);
            } else {
                $status = 1;
                $info = $transaction->id;
            }

            if (!$this->db->update(
                'UPDATE conversions SET status = ?, info = ? WHERE id = ?', [
                $status,
                $info,
                $conversion->id
            ])) {
                throw new \RuntimeException(sprintf('Cannot update conversion %s', $conversion->id));
            }

            ++$count;
        }

        return $count;
    }
}
