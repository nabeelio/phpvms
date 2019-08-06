<?php

/**
 * DO NOT MODIFY THIS FILE DIRECTLY!
 * It will just make upgrading (a little) more difficult
 *
 * Create a local.conf.php in the root (in the same directory
 * as your .env file), and then copy this file into there, and
 * make your changes there.
 */

return [
    /*
     * Check for if we're "installed" or not
     */
    'installed' => env('PHPVMS_INSTALLED', false),

    /*
     * Where to redirect after logging in
     */
    'login_redirect' => '/dashboard',

    /*
     * Where to redirect after registration
     */
    'registration_redirect' => '/profile',

    /*
     * The ISO "Currency Code" to use, the list is in config/money.php
     *
     * Note, do not change this after you've set it, unless you don't
     * care that the currencies aren't "exchanged" into the new format
     */
    'currency' => 'USD',

    /*
     * Point to the class to use to retrieve the METAR string. If this
     * goes inactive at some date, it can be replaced
     */
    'metar' => App\Services\Metar\AviationWeather::class,

    /*
     * Your vaCentral API key
     */
    'vacentral_api_key' => env('VACENTRAL_API_KEY', ''),

    /*
     * vaCentral API URL. You likely don't need to change this
     */
    'vacentral_api_url' => 'https://api.vacentral.net',

    /*
     * Misc Settings
     */
    'news_feed_url' => 'http://forum.phpvms.net/rss/1-announcements-feed.xml/?',

    /*
     * URL to the latest version file
     */
    'version_file' => 'https://api.github.com/repos/nabeelio/phpvms/releases',

    /*
     * Where the KVP file is stored
     */
    'kvp_storage_path' => storage_path('app/kvp.json'),

    /*
     * DO NOT CHANGE THESE! It will result in messed up data
     * The setting you're looking for is in the admin panel,
     * under settings, for the display units
     */
    'internal_units' => [
        'altitude' => 'feet',
        'distance' => 'nmi',
        'fuel'     => 'lbs',
        'mass'     => 'lbs',
        'velocity' => 'knots',
        'volume'   => 'gallons',
    ],

    /*
     * Avatar resize settings
     * feel free to edit the following lines.
     * Both parameters are in px.
     */
    'avatar' => [
        'width'  => '200',
        'height' => '200',
    ],
];
