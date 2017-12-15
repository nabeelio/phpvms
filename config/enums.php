<?php

return [

    'status' => [
        'INACTIVE' => 0,
        'DISABLED' => 0,
        'ACTIVE'   => 1,
        'ENABLED'  => 1,
    ],

    # Pilot states
    'states' => [
        'PENDING'   => 0,
        'ACTIVE'    => 1,
        'ON_LEAVE'  => 2,
        'SUSPENDED' => 3,
    ],

    'sources' => [
        'MANUAL' => 0,
        'ACARS'  => 1,
    ],

    'pirep_status' => [
        'PENDING'  => 0,
        'ACCEPTED' => 1,
        'REJECTED' => -1,
    ],

    'fuel_types' => [
        '100LL' => 0,
        'JETA'  => 1,
        'MOGAS' => 2,
    ],

    'days' => [
        'MONDAY'    => 1,
        'TUESDAY'   => 2,
        'WEDNESDAY' => 4,
        'THURSDAY'  => 8,
        'FRIDAY'    => 16,
        'SATURDAY'  => 32,
        'SUNDAY'    => 64
    ],

];

