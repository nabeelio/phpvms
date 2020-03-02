<?php

/**
 * phpVMS
 */

/**
 * If you move the public folder, or all the files in the public folder,
 * be sure to change this to point to the right place. View these docs:
 *
 * http://docs.phpvms.net/setup/installation/cpanel-setup
 */
$path_to_phpvms_folder = __DIR__.'/../';

require $path_to_phpvms_folder.'/bootstrap/autoload.php';

$app = require_once $path_to_phpvms_folder.'/bootstrap/app.php';
$app->setPublicPath(__DIR__);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
