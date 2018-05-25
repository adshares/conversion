<?php

namespace Adshares\Ads\Console\Commands;

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
        $host = $this->input->getOption('host');
        $port = $this->input->getOption('port');

        $this->info("Lumen development server started on http://{$host}:{$port}/");




    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['host', null, InputOption::VALUE_OPTIONAL, 'The ethereum node host address', 'localhost'],
            ['port', null, InputOption::VALUE_OPTIONAL, 'The ethereum node port', 8000],
        ];
    }
}
