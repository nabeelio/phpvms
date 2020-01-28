<?php

use App\Facades\Utils;

class UtilsTest extends TestCase
{
    public function testDates()
    {
        $carbon = new \Carbon\Carbon('2018-04-28T12:55:40Z');
        $this->assertNotNull($carbon);
    }

    /**
     * @throws Exception
     */
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

    public function testSecondsToTime()
    {
        $t = Utils::secondsToTimeString(3600);
        $this->assertEquals('1h 0m', $t);

        $t = Utils::secondsToTimeString(3720);
        $this->assertEquals('1h 2m', $t);

        $t = Utils::secondsToTimeString(3722);
        $this->assertEquals('1h 2m', $t);

        $t = Utils::secondsToTimeString(3722, true);
        $this->assertEquals('1h 2m 2s', $t);
    }

    public function testMinutesToTime()
    {
        $t = Utils::minutesToTimeParts(65);
        $this->assertEquals(['h' => 1, 'm' => 5], $t);

        $t = Utils::minutesToTimeString(65);
        $this->assertEquals('1h 5m', $t);

        $t = Utils::minutesToTimeString(43200);
        $this->assertEquals('720h 0m', $t);
    }

    public function testApiKey()
    {
        $api_key = Utils::generateApiKey();
        $this->assertNotNull($api_key);
    }

    public function testHexCode()
    {
        $hex_code = \App\Support\ICAO::createHexCode();
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
            $this->assertEquals('phpvms.net', \App\Support\Utils::getRootDomain($case));
        }

        $this->assertEquals('phpvms', \App\Support\Utils::getRootDomain('http://phpvms'));
    }
}
