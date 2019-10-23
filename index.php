<?php

/**
 * phpVMS (root folder)
 */
$path_to_phpvms_folder = __DIR__;

require $path_to_phpvms_folder.'/bootstrap/autoload.php';

$app = require_once $path_to_phpvms_folder.'/bootstrap/app.php';

$app->setPublicPath(__DIR__.'/public');
$app->setPublicUrlPath(env('APP_PUBLIC_URL', '/'));

// Uncomment this line if you're having issues with the redirecting not working properly
//$app->setPublicUrlPath(env('APP_PUBLIC_URL', '/public'));

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
