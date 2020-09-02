<?php

/**
 * phpVMS Entry Point
 * https://phpvms.net
 * https://docs.phpvms.net
 */

/**
 * NOTE!!
 *
 * If you move the public folder, or all the files in the public folder,
 * be sure to change this to point to the right place. View these docs:
 *
 * https://docs.phpvms.net/installation/uploading
 */
$path_to_phpvms_folder = __DIR__.'/../';
if (file_exists($path_to_phpvms_folder.'bootstrap/autoload.php')) {
    require $path_to_phpvms_folder.'bootstrap/autoload.php';
    $app = require_once $path_to_phpvms_folder.'bootstrap/app.php';
}

// Look up one more folder up (outside of the Laravel root) and in the `phpvms` subfolder
elseif (file_exists($path_to_phpvms_folder.'phpvms/bootstrap/autoload.php')) {
    require $path_to_phpvms_folder.'phpvms/bootstrap/autoload.php';
    $app = require_once $path_to_phpvms_folder.'phpvms/bootstrap/app.php';
}

// Bail out
else {
    echo 'Cannot find path to bootstrap/autoload.php. Modify this file to the proper path';
    exit();
}

$app->setPublicPath(__DIR__);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
