<?php

namespace App\Commands;

use App\Application;
use App\Telegram\TelegramApiImpl;

class TgMessagesCommand extends Command
{
    public function __construct(public Application $app) {}

    public function run(): void
    {
        $tgApi = new TelegramApiImpl($this->app->env('TELEGRAM_TOKEN'));
        echo json_encode($tgApi->getMessages(0));
    }
}
