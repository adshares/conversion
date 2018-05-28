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
        $this->info(sprintf('Scanner starting on %s', $url));

        $scanner = new Scanner($url);
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
            ['url', null, InputOption::VALUE_OPTIONAL, 'The ethereum node host URL', 'http://localhost:8545']
        ];
    }
}
