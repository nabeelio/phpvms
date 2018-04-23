<?php

/*
 * The keys can be created here
 * https://www.google.com/recaptcha/admin
 *
 * Don't edit this file directly, add the section to your config.php
 */
return [
    'enabled' => false,
    'sitekey' => '',
    'secret' => '',

    # Attributes can be found here:
    # https://developers.google.com/recaptcha/docs/display#render_param
    'attributes' => [
        'data-theme' => 'light',
    ],

    'options' => [
        'timeout' => 2.0,
    ],
];
