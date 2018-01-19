<?php

return [
    'tracking_id' => 'UA-100567975-1',
    'protocol_version' => 1,
    'is_ssl' => false,
    'is_disabled' => env('APP_ANALYTICS_DISABLED', false),
    'anonymize_ip' => false,
    'async_requests' => false,
];
