<?php

if (!defined('LUMEN_START')) {
    define('LUMEN_START', microtime(true));
}

include_once __DIR__.'/application.php';

$app = new Application();
$app->bindInterfaces();

return $app;
