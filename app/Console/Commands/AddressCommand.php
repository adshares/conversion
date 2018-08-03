<?php

namespace Adshares\Ads\Console\Commands;

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
                self::crc16(sprintf('%s%s', $account->node_id, $account->id))
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


    private static function crc16($hexChars)
    {
        $chars = hex2bin($hexChars);
        $crc = 0x1D0F;

        for ($i = 0; $i < strlen($chars); $i ++) {
            $x = ($crc >> 8) ^ ord($chars[$i]);
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ (($x << 12)) ^ (($x << 5)) ^ ($x)) & 0xFFFF;
        }
        return $crc;
    }
}
