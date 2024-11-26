<?php

namespace App\Actions;

use App\Models\Event;

class EventSaver
{
    public function __construct(private Event $event) {}

    public function handle(array $options): void
    {
        $this->saveEvent($options);
    }

    private function saveEvent(array $params): void
    {
        $this->event->insert(
            implode(', ', array_keys($params)),
            array_values($params)
        );
    }
}
