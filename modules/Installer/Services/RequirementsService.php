<?php

namespace Modules\Installer\Services;

use App\Contracts\Service;

class RequirementsService extends Service
{
    /**
     * Check the PHP version that it meets the minimum requirement
     *
     * @return array
     */
    public function checkPHPVersion(): array
    {
        $passed = false;
        if (version_compare(PHP_VERSION, config('installer.php.version')) >= 0) {
            $passed = true;
        }

        return ['version' => PHP_VERSION, 'passed' => $passed];
    }

    /**
     * Make sure the minimal extensions required are loaded
     *
     * @return array
     */
    public function checkExtensions(): array
    {
        $extensions = [];
        foreach (config('installer.extensions') as $ext) {
            $pass = true;
            if (!\extension_loaded($ext)) {
                $pass = false;
            }

            $extensions[] = [
                'ext'    => $ext,
                'passed' => $pass,
            ];
        }

        return $extensions;
    }

    /**
     * Check the permissions for the directories specified
     * Make sure they exist and are writable
     *
     * @return array
     */
    public function checkPermissions(): array
    {
        clearstatcache();

        $directories = [];
        foreach (config('installer.permissions') as $dir) {
            $pass = true;
            $path = base_path($dir);

            if (!file_exists($path)) {
                $pass = false;
            }

            if (!is_writable($path)) {
                $pass = false;
            }

            $directories[] = [
                'dir'    => $dir,
                'passed' => $pass,
            ];
        }

        return $directories;
    }
}
