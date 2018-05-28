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
    private $contractAddress = '0x422866a8f0b032c5cf1dfbdef31a20f4509562b0';

    /**
     * @var string
     */
    private $burnAddress = '0x0';

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
    public function scan()
    {
        $eth = $this->web3->eth;
        $this->logger->debug('Scanning...');

        $count = 0;

        $eth->newFilter([
            'fromBlock' => $this->startBlock,
            'topics' => [$this->transferTopic],
            'address' => $this->contractAddress
        ], function ($err, $result) use ($eth, &$count) {

            if ($err !== null) {
                $this->logger->error(sprintf('Creating filter error: %s', $err->getMessage()));

                return;
            }

            $eth->getFilterLogs($result, function ($err, $logs) use (&$count) {

                if ($err !== null) {
                    $this->logger->error(sprintf('Fetching logs error: %s', $err->getMessage()));

                    return;
                }

                $count = count($logs);
                $this->logger->info(sprintf('Found %d logs', count($logs)));
                foreach ($logs as $log) {
                    $this->logger->debug($log->transactionHash);
                }
            });
        });

        return $count;
    }
}
