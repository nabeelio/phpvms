<?php


/**
 * Class ImporterTest
 */
class ImporterTest extends TestCase
{
    protected $importerSvc;

    public function setUp()
    {
        $this->importerSvc = app(\App\Services\ImporterService::class);
    }

    /**
     * Test the parsing of different field/column which can be used
     * for specifying different field values
     */
    public function testMultiFieldValues()
    {
        $tests = [
            [
                'input' => 'gate',
                'expected' => 'gate'
            ],
            [
                'input' => 'gate;cost index',
                'expected' => [
                    'gate',
                    'cost index',
                ]
            ],
            [
                'input' => 'gate=B32;cost index=100',
                'expected' => [
                    'gate' => 'B32',
                    'cost index' => '100'
                ]
            ],
            [
                'input' => 'Y?price=200&cost=100; F?price=1200',
                'expected' => [
                    'Y' => [
                        'price' => 200,
                        'cost' => 100,
                    ],
                    'F' => [
                        'price' => 1200
                    ]
                ]
            ],
            [
                'input' => 'Y?price&cost; F?price=1200',
                'expected' => [
                    'Y' => [
                        'price',
                        'cost',
                    ],
                    'F' => [
                        'price' => 1200
                    ]
                ]
            ]
        ];

        foreach($tests as $test) {
            $parsed = $this->importerSvc->parseMultiColumnValues($test['input']);
            $this->assertEquals($parsed, $test['expected']);
        }
    }
}
