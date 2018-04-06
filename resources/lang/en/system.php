<?php

return [

    'global' => [
        'active'   => 'Active',
        'inactive' => 'Inactive'
    ],

    'aircraft' => [
        'status' => [
            'active'   => 'Active',
            'stored'   => 'Stored',
            'retired'  => 'Retired',
            'scrapped' => 'Scrapped',
            'written'  => 'Written Off',
        ],
    ],

    'days' => [
        'mon'   => 'Monday',
        'tues'  => 'Tuesday',
        'wed'   => 'Wednesday',
        'thurs' => 'Thursday',
        'fri'   => 'Friday',
        'sat'   => 'Saturday',
        'sun'   => 'Sunday',
    ],

    'expenses' => [
        'type' => [
            'flight'  => 'Flight',
            'daily'   => 'Daily',
            'monthly' => 'Monthly',
        ],
    ],

    'flights' => [
        'type' => [
            'pass_scheduled'    => 'Passenger - Scheduled',
            'cargo_scheduled'   => 'Cargo - Scheduled',
            'charter_pass_only' => 'Charter - Passenger Only',
            'addtl_cargo_mail'  => 'Additional Cargo/Mail',
            'special_vip'       => 'Special VIP Flight (FAA/Government)',
            'pass_addtl'        => 'Passenger - Additional',
            'charter_cargo'     => 'Charter - Cargo/Mail',
            'ambulance'         => 'Ambulance Flight',
            'training_flight'   => 'Training Flight',
            'mail_service'      => 'Mail Service',
            'charter_special'   => 'Charter w/ Special Handling',
            'positioning'       => 'Positioning (Ferry/Delivery/Demo)',
            'technical_test'    => 'Technical Test',
            'military'          => 'Military',
            'technical_stop'    => 'Technical Stop',
        ],
    ],

    'pireps' => [
        'source' => [
            'manual' => 'Manual',
            'acars'  => 'ACARS',
        ],
        'state' => [
            'accepted'    => 'Accepted',
            'pending'     => 'Pending',
            'rejected'    => 'Rejected',
            'in_progress' => 'In Progress',
            'cancelled'   => 'Cancelled',
            'deleted'     => 'Deleted',
            'draft'       => 'Draft',
        ],
        'status' => [
            'initialized' => 'Initiated',
            'scheduled'   => 'Scheduled',
            'boarding'    => 'Boarding',
            'ready_start' => 'Ready for start',
            'departed'    => 'Departed',
            'ready_deice' => 'Ready for de-icing',
            'deicing'     => 'De-icing in progress',
            'ground_ret'  => 'Ground return',
            'enroute'     => 'Enroute',
            'diverted'    => 'Diverted',
            'approach'    => 'Approach',
            'final_appr'  => 'Final approach',
            'landed'      => 'Landed',
            'arrived'     => 'Arrived',
            'cancelled'   => 'Cancelled',
        ]
    ],

    'users' => [
        'state' => [
            'pending'   => 'Pending',
            'active'    => 'Active',
            'rejected'  => 'Rejected',
            'on_leave'  => 'On Leave',
            'suspended' => 'Suspended',
        ],
    ],
];
