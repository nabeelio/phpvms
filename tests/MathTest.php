<?php

namespace Tests;

use App\Support\Math;
use App\Support\Units\Distance;

class MathTest extends TestCase
{
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

        foreach ($tests as $test) {
            $this->assertEquals($test['expected'], $test['fn']);
        }
    }

    public function testDistanceMeasurement()
    {
        $dist = new Distance(1, 'mi');
        $this->assertEquals(1609.34, $dist['m']);
        $this->assertEquals(1.61, $dist['km']);
    }
}
