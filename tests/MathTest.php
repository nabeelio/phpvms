<?php

use App\Support\Math;

class MathTest extends TestCase
{
    public function setUp() {
    }

    /**
     * Test adding/subtracting a percentage
     */
    public function testAddPercent()
    {
        $tests = [
            ['expected' => 112, 'fn' => Math::addPercent(100, 12)],
            ['expected' => 112, 'fn' => Math::addPercent(100, '12')],
            ['expected' => 112, 'fn' => Math::addPercent(100, '12%')],
            ['expected' => 112, 'fn' => Math::addPercent(100, '12 %')],
            ['expected' => 112, 'fn' => Math::addPercent('100 ', '12')],
            ['expected' => 112.5, 'fn' => Math::addPercent('100', '12.5')],
            ['expected' => 88, 'fn' => Math::addPercent('100', -12)],
            ['expected' => 88, 'fn' => Math::addPercent('100', '-12')],
            ['expected' => 88, 'fn' => Math::addPercent('100', '-12 %')],
            ['expected' => 88, 'fn' => Math::addPercent('100', '-12%')],
        ];

        foreach($tests as $test) {
            $this->assertEquals($test['expected'], $test['fn']);
        }
    }

}
