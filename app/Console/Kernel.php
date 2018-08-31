<?php

namespace Adshares\Ads\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'Adshares\Ads\Console\Commands\EthScanCommand',
        'Adshares\Ads\Console\Commands\AdsScanCommand',
        'Adshares\Ads\Console\Commands\AddressCommand'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    public static function crc16($hexChars)
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
