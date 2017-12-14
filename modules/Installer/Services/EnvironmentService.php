<?php

namespace Modules\Installer\Services;

use Illuminate\Encryption\Encrypter;
use Log;
use PDO;

class EnvironmentService
{

    /**
     * Check the PHP version that it meets the minimum requirement
     * @return boolean
     */
    public function createEnvFile($type, $host, $port, $name, $user, $pass)
    {
        $env_opts = [
            'db_conn' => $type,
            'db_host' => $host,
            'db_port' => $port,
            'db_name' => $name,
            'db_user' => $user,
            'db_pass' => $pass,
        ];

        $env_opts['app_key'] = base64_encode(Encrypter::generateKey(config('app.cipher')));

        $this->writeEnvFile($env_opts);
        return true;
    }

    /**
     * Get the template file name and write it out
     */
    protected function writeEnvFile($env_opts)
    {
        $app = app();
        $env_file = $app->environmentFilePath();

        # TODO: Remove this post-testing
        $env_file .= '.generated';

        $env_contents = view('installer::stubs/env', $env_opts);
        Log::info($env_contents);

        $fp = fopen($env_file, 'w');
        fwrite($fp, $env_contents);
        fclose($fp);
    }

}
