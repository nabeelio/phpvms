<?php

use \App\Support\Metar;

/**
 * Test the parsing/support class of the metar
 */
class MetarTest extends TestCase
{
    /**
     * Test adding/subtracting a percentage
     */
    public function testMetar1()
    {
        $metar =
            'KJFK 042151Z 28026G39KT 10SM '
            .'FEW055 SCT095 BKN110 BKN230 12/M04 '
            .'A2958 RMK AO2 PK WND 27045/2128 PRESRR '
            .'SLP018 T01221044';

        $parsed = new Metar($metar);

        /*
            Conditions  VFR visibility 10NM
            Barometer   1001.58 Hg / 29.58 MB
            Clouds      FEW @ 5500 ft
                        SCT @ 9500 ft
                        BKN @ 11000 ft
                        BKN @ 23000 ft
            Wind        26 kts @ 280° gusts to 39
         */
    }

    public function testMetar2()
    {
        $metar =
            'CYWG 172000Z 30015G25KT 3/4SM R36/4000FT/D -SN '
            .'BLSN BKN008 OVC040 M05/M08 A2992 REFZRA WS RWY36 '
            .'RMK SF5NS3 SLP134';
    }
}
