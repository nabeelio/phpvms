<?php

/*
 * The keys can be created here
 * https://www.google.com/recaptcha/admin
 *
 * Don't edit this file directly, add the section to your config.php
 */
return [
    'enabled' => env('CAPTCHA_ENABLED', false),
    'sitekey' => env('CAPTCHA_SITEKEY', ''),
    'secret'  => env('CAPTCHA_SECRET', ''),

    // Attributes can be found here:
    // https://developers.google.com/recaptcha/docs/display#render_param
    'attributes' => [
        'data-theme' => env('CAPTCHA_DATATHEME', 'light'),
    ],

    'options' => [
        'timeout' => env('CAPTCHA_TIMEOUT', '2.0'),
    ],
];
