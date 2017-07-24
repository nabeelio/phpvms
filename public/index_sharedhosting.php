<?php

/**
 * Instructions on shared hosting...
 * Assuming you have a structure like this:
 * /home/your_user
 *      /public_html
 *      /public_ftp
 *
 * Place your PHPVMS folder so it's on the same level
 * as public_html, so it looks like:
 *
 * /home/your_user
 *      /public_html
 *      /public_ftp
 *      /phpvms
 *
 * Follow these steps:
 *
 * 1. Copy everything from the phpvms/public folder into
 *    public_html/
 * 2. Rename this file (index_sharedhosting.php) to index.php
 * 3. ???
 */


require __DIR__.'/../../bootstrap/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
