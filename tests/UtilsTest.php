<?php

use App\Facades\Utils;

class UtilsTest extends TestCase
{
    public function setUp() {
    }

    public function testSecondsToTime() {
        print_r(Utils::secondsToTime(3600));
    }
}
