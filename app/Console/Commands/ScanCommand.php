<?php

namespace Adshares\Ads\Console\Commands;

use Log;
use Adshares\Ads\Scanner\Scanner;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ScanCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan the ethereum blockchain for burned tokens';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $url = $this->input->getOption('url');
        if (null === $url) {
            $url = env('ADS_NODE_URL');
        }

        $this->info(sprintf('Scanner starting on %s', $url));

        $scanner = new Scanner($url, app('db'));

        $scanner->setStartBlock(env('ADS_START_BLOCK'));
        $scanner->setTransferTopic(env('ADS_TRANSFER_TOPIC'));
        $scanner->setTransferMethod(env('ADS_TRANSFER_METHOD'));
        $scanner->setContractAddress(env('ADS_CONTRACT_ADDRESS'));
        $scanner->setBurnAddress(env('ADS_BURN_ADDRESS'));
        $scanner->setLogger(app('log'));

        $count = $scanner->scan();

        $this->info(sprintf('Converted %d transactions', $count));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['url', null, InputOption::VALUE_OPTIONAL, 'The ethereum node host URL']
        ];
    }
}
