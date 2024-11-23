<?php

namespace App\Commands;

use App\Application;
use App\Database\SQLite;
use App\EventSender\EventSender;
use App\Models\Event;
use App\Telegram\TelegramApiImpl;

class TgMessagesCommand extends Command
{
    protected Application $app;
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function run(array $options = []): void
    {
        $tgApi = new TelegramApiImpl($this->app->env('TELEGRAM_TOKEN'));
        $eventSender = new EventSender(new TelegramApiImpl($this->app->env('TELEGRAM_TOKEN')));
        $oldMessages = [];
        $offset = 0;
        while (true) {
            $messages = [];

            $result = $tgApi->getMessages($offset);
            $offset = $result['offset'];

            foreach ($result['result'] as $chatId => $newMessage) {
                if (isset($oldMessages[$chatId])) {
                    $oldMessages[$chatId] = [...$oldMessages[$chatId], ...$newMessage];
                } else {
                    $oldMessages[$chatId] = $newMessage;
                }

                $messages[$chatId] = $oldMessages[$chatId];
            }

            foreach ($messages as $userId => $userMessages) {
                $userAnswers = [];
                foreach ($userMessages as $userMessage) {
                    if ($userMessage === '/start') {
                        $userAnswers = [];
                    } else {
                        $userAnswers[] = $userMessage;
                    }
                }

                $message = match (count($userAnswers)) {
                    4 => $this->createEventAndReturnMessage($userAnswers),
                    3 => 'Укажите в какие дни Вам нужно отправлять сообщения в формате cron',
                    2 => 'Укажите текст напоминания',
                    1 => 'Укажите ID пользователя. Его можно узнать, переслав любое сообщение пользователя боту @myidbot .',
                    0 => 'Укажите название события.',
                    default => 'Некорректный ввод. Начните заново, отправив /start.',
                };

                $eventSender->sendMessage($userId, $message);
            }
        }
    }

    private function createEventAndReturnMessage(array $userAnswers)
    {
        $params = [
            'name' => $userAnswers[0],
            'receiver_id' => $userAnswers[1],
            'text' => $userAnswers[2],
        ];

        $cronValues = $this->getCronValues($userAnswers[3]);

        if (count($cronValues) != 5) {
            return 'Некорректный ввод формата cron. Начните заново, отправив /start.';
        }

        $params['minute'] = $cronValues[0];
        $params['hour'] = $cronValues[1];
        $params['day'] = $cronValues[2];
        $params['month'] = $cronValues[3];
        $params['day_of_week'] = $cronValues[4];

        $this->saveEvent($params);

        return 'Я записал Ваше событие. Для нового события введите /start .';
    }

    private function getCronValues(string $cronString): array
    {
        $cronValues = explode(" ", $cronString);

        $cronValues = array_map(function ($item) {
            return $item === "*" ? null : $item;
        }, $cronValues);

        return $cronValues;
    }

    private function saveEvent(array $params): void
    {
        $event = new Event(new SQLite($this->app));

        $event->insert(
            implode(', ', array_keys($params)),
            array_values($params)
        );
    }
}
