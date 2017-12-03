<?php

use App\Facades\Utils;

class UtilsTest extends TestCase
{
    public function setUp() {
    }

    public function testSecondsToTimeParts()
    {
        $t = Utils::secondsToTimeParts(3600);
        $this->assertEquals(['h' => 1, 'm' => 0, 's' => 0], $t);

        $t = Utils::secondsToTimeParts(3720);
        $this->assertEquals(['h' => 1, 'm' => 2, 's' => 0], $t);

        $t = Utils::secondsToTimeParts(3722);
        $this->assertEquals(['h' => 1, 'm' => 2, 's' => 2], $t);

        $t = Utils::secondsToTimeParts(60);
        $this->assertEquals(['h' => 0, 'm' => 1, 's' => 0], $t);

        $t = Utils::secondsToTimeParts(62);
        $this->assertEquals(['h' => 0, 'm' => 1, 's' => 2], $t);
    }

    public function testSecondsToTime() {
        $t = Utils::secondsToTime(3600);
        $this->assertEquals('1h 0m', $t);

        $t = Utils::secondsToTime(3720);
        $this->assertEquals('1h 2m', $t);

        $t = Utils::secondsToTime(3722);
        $this->assertEquals('1h 2m', $t);

        $t = Utils::secondsToTime(3722, true);
        $this->assertEquals('1h 2m 2s', $t);
    }
}
