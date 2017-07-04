<?php

# TODO: Remove this ugly hackiness
if(defined('VMSEnums')) {
    return;
} else {
    define('VMSEnums', true);
}

class VMSEnums
{
    public static $sources
        = [
            'MANUAL' => 0,
            'ACARS'  => 1,
        ];

    public static $pirep_status
        = [
            'PENDING'  => 0,
            'ACCEPTED' => 1,
            'REJECTED' => -1,
        ];

    public static $fuel_types
        = [
            '100LL' => 0,
            'JETA'  => 1,
            'MOGAS' => 2,
        ];
}
