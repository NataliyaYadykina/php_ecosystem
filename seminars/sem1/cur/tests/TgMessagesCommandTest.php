<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TgMessagesCommandTest extends TestCase
{
    // #[DataProvider('cronValuesDataProvider')]
    /**
     * @dataProvider cronValuesDataProvider
     */
    public function testGetCronValuesHasCorrectStringExpectCorrectArray(string $cronString, array $cronExpectedArray): void
    {
        $saveCommand = new \App\Commands\TgMessagesCommand(new \App\Application(dirname(__DIR__)));

        $cronActualArray = $saveCommand->getCronValues($cronString);

        $this->assertEquals($cronExpectedArray, $cronActualArray);
    }

    public static function cronValuesDataProvider(): array
    {
        return [
            '* * * * *' => ['* * * * *', [null, null, null, null, null]],
            '1 * * * *' => ['1 * * * *', [1, null, null, null, null]],
            '1 2 * * *' => ['1 2 * * *', [1, 2, null, null, null]],
            '1 2 3 * *' => ['1 2 3 * *', [1, 2, 3, null, null]],
            '1 2 3 4 *' => ['1 2 3 4 *', [1, 2, 3, 4, null]],
            '1 2 3 4 5' => ['1 2 3 4 5', [1, 2, 3, 4, 5]],
            '1 2 3 4' => ['1 2 3 4', [1, 2, 3, 4]],
            '1 2 *' => ['1 2 *', [1, 2, null]],
            '* *' => ['* *', [null, null]],
            '*' => ['*', [null]],
            '10' => ['10', [10]],
        ];
    }
}
