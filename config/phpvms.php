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
     * The skin to use for the front-end
     */
    'skin' => 'default',

    /**
     * Start date. Set the date of when your VA has started
     * Used as an anchor point for some financials and things
     *
     * YYYY-MM-DD format
     */
    'start_date' => '2017-07-07',

    /**
     * Pick one of:
     * dollar, euro, gbp, yen, jpy, rupee, ruble
     */
    'currency' => 'dollar',

    /**
     * Misc Settings
     */

    'feed_url' => 'http://forum.phpvms.net/rss/1-announcements-feed.xml/?',

];
