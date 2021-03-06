<?php

namespace Adshares\Ads\Console\Commands;

use Adshares\Ads\Console\Kernel;
use Illuminate\Console\Command;
use PHPMailer\PHPMailer\PHPMailer;

class NotifyCommand extends Command
{
    const CMD_HOST = 'n02.e11.click';
    const CMD_ADDRESS = '0002-00000001-659C';

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
              tx_hash,
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

        if (0 === count($waiting) && 0 === (count($errors))) {
            return;
        }

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
                str_repeat('=', 96)
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
                    '; echo \'{"run":"send_one","address":"%s","amount":%d,"message":"%s"}\'',
                    $conversion->ads_address,
                    $conversion->amount,
                    str_replace('0X', '', strtoupper($conversion->tx_hash))
                );
            }

            $waitingMessage .= "\n\n";
            $commandMessage = "### Command ###\n\n";
            $commandMessage .= sprintf(
                '(echo \'{"run":"get_me"}\'%s) | ads --work-dir=. --host=%s --address=%s --secret',
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
                str_repeat('=', 96)
            );

            foreach ($errors as $conversion) {
                $errorsMessage .= sprintf(
                    "%19s | %42s | %8d | %18s\nErr: %s\n%s\n",
                    $conversion->log_date,
                    $conversion->from_address,
                    $conversion->amount,
                    $conversion->ads_address,
                    implode("\n     ", str_split($conversion->info, 91)),
                    str_repeat('-', 96)
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
        $this->info('Sending message...');

        try {
            $this->sendMail($title, $message);
            $this->info('Message sent');
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            app('log')->error(sprintf('Sending message failed: %s', $e->getMessage()));
            $this->info('Sending message failed');
        }
    }

    /**
     * @param $title
     * @param $message
     * @return int
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendMail($title, $message): int
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = env('SMTP_HOST');
        $mail->Port = env('SMTP_PORT');
        $mail->SMTPSecure = env('SMTP_SECURITY');

        $mail->setFrom(env('SMTP_FROM'));
        $mail->addAddress(env('NOTIFY_TO'));

        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = sprintf('<pre>%s</pre>', $message);
        $mail->AltBody = $message;

        return $mail->send();
    }

}
