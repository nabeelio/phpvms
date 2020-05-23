<?php

namespace Tests;

use App\Support\ICAO;
use App\Support\Units\Time;
use App\Support\Utils;
use Carbon\Carbon;

class UtilsTest extends TestCase
{
    public function testDates()
    {
        $carbon = new Carbon('2018-04-28T12:55:40Z');
        $this->assertNotNull($carbon);
    }

    /**
     * @throws \Exception
     */
    public function testSecondsToTimeParts()
    {
        $t = Time::secondsToTimeParts(3600);
        $this->assertEquals(['h' => 1, 'm' => 0, 's' => 0], $t);

        $t = Time::secondsToTimeParts(3720);
        $this->assertEquals(['h' => 1, 'm' => 2, 's' => 0], $t);

        $t = Time::secondsToTimeParts(3722);
        $this->assertEquals(['h' => 1, 'm' => 2, 's' => 2], $t);

        $t = Time::secondsToTimeParts(60);
        $this->assertEquals(['h' => 0, 'm' => 1, 's' => 0], $t);

        $t = Time::secondsToTimeParts(62);
        $this->assertEquals(['h' => 0, 'm' => 1, 's' => 2], $t);
    }

    /**
     * @throws \Exception
     */
    public function testSecondsToTime()
    {
        $t = Time::secondsToTimeString(3600);
        $this->assertEquals('1h 0m', $t);

        $t = Time::secondsToTimeString(3720);
        $this->assertEquals('1h 2m', $t);

        $t = Time::secondsToTimeString(3722);
        $this->assertEquals('1h 2m', $t);

        $t = Time::secondsToTimeString(3722, true);
        $this->assertEquals('1h 2m 2s', $t);
    }

    public function testMinutesToTime()
    {
        $t = Time::minutesToTimeParts(65);
        $this->assertEquals(['h' => 1, 'm' => 5], $t);

        $t = Time::minutesToTimeString(65);
        $this->assertEquals('1h 5m', $t);

        $t = Time::minutesToTimeString(43200);
        $this->assertEquals('720h 0m', $t);
    }

    public function testApiKey()
    {
        $api_key = Utils::generateApiKey();
        $this->assertNotNull($api_key);
    }

    /**
     * @throws \Exception
     */
    public function testHexCode()
    {
        $hex_code = ICAO::createHexCode();
        $this->assertNotNull($hex_code);
    }

    public function testGetDomain()
    {
        $tests = [
            'http://phpvms.net',
            'https://phpvms.net',
            'phpvms.net',
            'https://phpvms.net/index.php',
            'https://demo.phpvms.net',
            'https://demo.phpvms.net/file/index.php',
        ];

        foreach ($tests as $case) {
            $this->assertEquals('phpvms.net', Utils::getRootDomain($case));
        }

        $this->assertEquals('phpvms.co.uk', Utils::getRootDomain('http://phpvms.co.uk'));
        $this->assertEquals('phpvms.co.uk', Utils::getRootDomain('http://www.phpvms.co.uk'));
    }
}
