<?php

namespace Adshares\Ads\Console\Commands;

use Adshares\Ads\Scanner\EthScanner;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class EthScanCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'scan:eth';

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
    public function handle()
    {
        $url = $this->input->getOption('url');
        if (null === $url) {
            $url = env('ADST_NODE_URL');
        }

        $this->info(sprintf('EthScanner starting on %s', $url));

        $scanner = new EthScanner($url, app('db'));

        $scanner->setStartBlock(env('ADST_START_BLOCK'));
        $scanner->setTransferTopic(env('ADST_TRANSFER_TOPIC'));
        $scanner->setTransferMethod(env('ADST_TRANSFER_METHOD'));
        $scanner->setContractAddress(env('ADST_CONTRACT_ADDRESS'));
        $scanner->setBurnAddress(env('ADST_BURN_ADDRESS'));
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
