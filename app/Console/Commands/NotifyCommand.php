<?php

namespace Adshares\Ads\Console\Commands;

use Adshares\Ads\Console\Kernel;
use Illuminate\Console\Command;

class NotifyCommand extends Command
{
    const CMD_HOST = 'n01.e11.click';
    const CMD_ADDRESS = '0001-00000001-8B4E';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify about waiting conversions';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info(sprintf('Getting conversions'));

        $db = app('db');
        $conversions = $db->select(
            'SELECT
              log_date,
              from_address,
              amount,
              ads_address,
              status,
              info
            FROM conversions
            WHERE status != 1
            ORDER BY log_date DESC');


        $waiting = [];
        $errors = [];

        foreach ($conversions as $conversion) {
            if (0 === $conversion->status) {
                $waiting[] = $conversion;
            } else {
                $errors[] = $conversion;
            }
        }

        $title = sprintf(
            'ADS conversion - %d waiting & %d errors',
            count($waiting),
            count($errors)
        );
        $this->info($title);
        app('log')->info($title);

        $waitingMessage = '';
        $commandMessage = '';
        $errorsMessage = '';

        if (count($waiting) > 0) {
            $waitingMessage = "### Waiting ###\n\n";
            $waitingMessage .= sprintf(
                "%-19s | %-42s | %-8s | %-18s\n%s\n",
                'Date',
                'ETH Address',
                'Amount',
                'ADS Address',
                str_repeat('=',96)
            );
            $transfers = '';

            foreach ($waiting as $conversion) {
                $waitingMessage .= sprintf(
                    "%19s | %42s | %8d | %18s\n",
                    $conversion->log_date,
                    $conversion->from_address,
                    $conversion->amount,
                    $conversion->ads_address

                );
                $transfers .= sprintf(
                    '; echo \'{"run":"send_one","address":"%s","amount":%d}\'',
                    $conversion->ads_address,
                    $conversion->amount
                );
            }

            $waitingMessage .= "\n\n";
            $commandMessage = "### Command ###\n\n";
            $commandMessage .= sprintf(
                '(echo \'{"run":"get_me"}\'%s) | ads --work-dir=. --host=%s --address=%s --secret=',
                $transfers,
                self::CMD_HOST,
                self::CMD_ADDRESS
            );
        }

        if (count($errors) > 0) {
            $errorsMessage = "### Errors ###\n\n";
            $errorsMessage .= sprintf(
                "%-19s | %-42s | %-8s | %-18s\n%s\n",
                'Date',
                'ETH Address',
                'Amount',
                'ADS Address',
                str_repeat('=',96)
            );

            foreach ($errors as $conversion) {
                $errorsMessage .= sprintf(
                    "%19s | %42s | %8d | %18s\nErr: %s\n%s\n",
                    $conversion->log_date,
                    $conversion->from_address,
                    $conversion->amount,
                    $conversion->ads_address,
                    implode("\n     ", str_split($conversion->info, 91)),
                    str_repeat('-',96)
                );
            }

            $errorsMessage .= "\n\n";
        }

        $message = sprintf(
            '%s%s%s',
            $waitingMessage,
            $errorsMessage,
            $commandMessage
        );

        app('log')->debug($message);
    }

}
