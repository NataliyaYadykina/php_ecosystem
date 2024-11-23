<?php

use PHPUnit\Framework\TestCase;
// use PHPUnit\Framework\Attributes\DataProvider;
// use PHPUnit\Framework\Attributes\TestWith;

class SaveEventCommandTest extends TestCase
{
    public function testRunWithoutParamsEchoHelp(): void
    {
        $this->expectOutputRegex('/СПРАВКА/');

        $saveCommand = new \App\Commands\SaveEventCommand(new \App\Application(dirname(__DIR__)));

        $saveCommand->run();
    }

    public function testShowHelpEchoHelp(): void
    {
        $this->expectOutputRegex('/СПРАВКА/');

        $saveCommand = new \App\Commands\SaveEventCommand(new \App\Application(dirname(__DIR__)));

        $saveCommand->showHelp();
    }

    public function testIsNeedHelpDontSentRequiredParamsReturnTrue(): void
    {
        $params = [];

        $saveCommand = new \App\Commands\SaveEventCommand(new \App\Application(dirname(__DIR__)));

        $result = $saveCommand->isNeedHelp($params);

        $this->assertTrue($result);
    }


    /**
     * @testWith ["h"]
     *           ["help"]
     */
    public function testIsNeedHelpSentHelpParamsReturnTrue(string $paramName): void
    {
        $params = [
            $paramName => null,
        ];

        $saveCommand = new \App\Commands\SaveEventCommand(new \App\Application(dirname(__DIR__)));

        $result = $saveCommand->isNeedHelp($params);

        $this->assertTrue($result);
    }

    public function testIsNeedHelpSentRequiredParamsReturnFalse(): void
    {
        $params = [
            'name' => '',
            'text' => '',
            'receiver' => '',
            'cron' => '',
        ];

        $saveCommand = new \App\Commands\SaveEventCommand(new \App\Application(dirname(__DIR__)));

        $result = $saveCommand->isNeedHelp($params);

        $this->assertFalse($result);
    }

    //	#[TestWith(['* * * * *', [null, null, null, null, null]])]
    //	#[TestWith(['1 * * * *', [1, null, null, null, null]])]
    //	#[TestWith(['1 2 * * *', [1, 2, null, null, null]])]
    //	#[TestWith(['1 2 3 * *', [1, 2, 3, null, null]])]
    //	#[TestWith(['1 2 3 4 *', [1, 2, 3, 4, null]])]
    //	#[TestWith(['1 2 3 4 5', [1, 2, 3, 4, 5]])]
    // #[DataProvider('cronValuesDataProvider')]
    /**
     * @dataProvider cronValuesDataProvider
     */
    public function testGetCronValuesHasCorrectStringExpectCorrectArray(string $cronString, array $cronExpectedArray): void
    {
        $saveCommand = new \App\Commands\SaveEventCommand(new \App\Application(dirname(__DIR__)));

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

    /**
     * @dataProvider isNeedHelpDataProvider
     */
    public function testIsNeedHelp(array $options, bool $isNeedHelp)
    {
        $saveEventCommand = new \App\Commands\SaveEventCommand(new \App\Application(dirname(__DIR__)));
        $result = $saveEventCommand->isNeedHelp($options);
        self::assertEquals($result, $isNeedHelp);
    }

    public static function isNeedHelpDataProvider(): array
    {
        return [
            [
                [
                    'name' => 'some name',
                    'text' => 'some text',
                    'receiver' => 'some receiver',
                    'cron' => 'some crone',
                    // 'help',
                    // 'h'
                ],
                false
            ],
            [
                [
                    'name' => 'some name',
                    'text' => 'some text',
                    'receiver' => 'some receiver',
                    'cron' => 'some crone',
                    'help' => 'some help',
                    'h' => null
                ],
                true
            ],
            [
                [
                    'name' => 'some name',
                    'text' => 'some text',
                    'receiver' => 'some receiver',
                    'cron' => 'some crone',
                    'help' => null,
                    'h' => 'some h'
                ],
                true
            ],
            [
                [
                    'name' => 'some name',
                    'text' => 'some text',
                    'receiver' => 'some receiver',
                    'cron' => null,
                ],
                true
            ],
            [
                [
                    'name' => 'some name',
                    'text' => 'some text',
                    'receiver' => null,
                    'cron' => 'some cron',
                ],
                true
            ],
        ];
    }
}
