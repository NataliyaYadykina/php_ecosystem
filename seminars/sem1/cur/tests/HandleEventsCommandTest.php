<?php

use PHPUnit\Framework\TestCase;

class HandleEventsCommandTest extends TestCase
{
    /**
     * @dataProvider eventDtoDataProvider
     */
    public function testShouldEventBeRanReceiveEventDtoAndReturnCorrectBool(array $event, bool $shouldEventBeRan): void
    {
        // die(var_dump(123, $event, $shouldEventBeRan));
        $handleEventsCommand = new \App\Commands\HandleEventsCommand(new \App\Application(dirname(__DIR__)));

        $result = $handleEventsCommand->shouldEventBeRan($event);

        self::assertEquals($result, $shouldEventBeRan);
    }

    public static function eventDtoDataProvider(): array
    {
        return [
            [
                [
                    'minute' => date("i"),
                    'hour' => date("H"),
                    'day' => date("d"),
                    'month' => date("m"),
                    'day_of_week' => date("w")
                ],
                true
            ],
            [
                [
                    'minute' => date("i"),
                    'hour' => date("H"),
                    'day' => date("d"),
                    'month' => date("m"),
                    'day_of_week' => null
                ],
                false
            ],
            [
                [
                    'minute' => date("i"),
                    'hour' => date("H"),
                    'day' => date("d"),
                    'month' => null,
                    'day_of_week' => date("w")
                ],
                false
            ],
        ];
    }
}
