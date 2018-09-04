<?php

namespace Adshares\Ads\Console\Commands;

use Adshares\Ads\Scanner\AdsScanner;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class AdsScanCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'scan:ads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan the ADS blockchain for conversions';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $host = $this->input->getOption('host');
        if (null === $host) {
            $host = env('ADS_NODE_HOST');
        }

        $this->info(sprintf('AdsScanner starting on %s', $host));

        $scanner = new AdsScanner(
            $host,
            (int)env('ADS_NODE_PORT'),
            env('ADS_ADDRESS'),
            env('ADS_SECRET'),
            app('db'),
            app('log')
        );

        $scanner->setTreasuryAddress(env('ADS_TREASURY_ADDRESS'));
        $scanner->setStartBlock((int)env('ADS_START_BLOCK'));

        $count = $scanner->scan();
        $this->info(sprintf('Found %d transactions', $count));

        $count = $scanner->pair();
        $this->info(sprintf('Paired %d transactions', $count));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['host', null, InputOption::VALUE_OPTIONAL, 'The ADS node host']
        ];
    }
}
