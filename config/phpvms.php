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

    /**
     * Check for if we're "installed" or not
     */
    'installed' => env('PHPVMS_INSTALLED', false),

    /**
     * The skin to use for the front-end
     */
    'skin' => env('APP_SKIN', 'default'),

    /**
     * Your vaCentral API key
     */
    'vacentral_api_key' => env('VACENTRAL_API_KEY', ''),

    /**
     * vaCentral API URL. You likely don't need to change this
     */
    'vacentral_api_url' => 'https://api.vacentral.net',

    /**
     * Misc Settings
     */
    'news_feed_url' => 'http://forum.phpvms.net/rss/1-announcements-feed.xml/?',
];
