<?php

namespace Adshares\Ads\Console\Commands;

use Adshares\Ads\Console\Kernel;
use Illuminate\Console\Command;

class AddressCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate accounts address';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info(sprintf('Generating addresses'));

        $db = app('db');
        $accounts = $db->select(
            'SELECT
              node_id,
              id
            FROM genesis_accounts
            ORDER BY node_id, id ASC');


        foreach ($accounts as $account) {

            $address = sprintf(
                '%s-%s-%04X',
                $account->node_id,
                $account->id,
                Kernel::crc16(sprintf('%s%s', $account->node_id, $account->id))
            );

            $db->update(
                'UPDATE genesis_accounts 
                  SET address = ?
                  WHERE node_id = ? AND id = ?',
                [
                    $address,
                    $account->node_id,
                    $account->id
                ]);
        }

        $this->info(sprintf('Generated %d addresses', count($accounts)));
    }

}
