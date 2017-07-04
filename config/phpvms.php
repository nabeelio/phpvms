<?php

return [

    /**
     * Pick one of:
     *  dollar, euro, gbp, yen, jpy, rupee, ruble
     */
    'currency' => env('PHPVMS_CURRENCY', 'dollar'),

    'cache_keys' => [
        'RANKS_PILOT_LIST' => [
            'key' => 'ranks::pilot_list',
            'time' => 1440,
        ]
    ]
];
