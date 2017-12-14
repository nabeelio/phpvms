<?php

/**
 * phpVMS
 */

/**
 * If you move the public folder, or all the files in the public
 * folder, be sure to go into the bootstrap/app.php file, and change
 * the 'path.public' path to where it lives.
 */

require __DIR__.'/../bootstrap/autoload.php';

/**
 * @var $app Illuminate\Foundation\Application
 */
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
