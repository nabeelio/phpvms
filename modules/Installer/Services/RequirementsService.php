<?php

namespace Modules\Installer\Services;


class RequirementsService {

    /**
     * Check the PHP version that it meets the minimum requirement
     * @return array
     */
    public function checkPHPVersion(): array
    {
        $passed = false;
        if(version_compare(PHP_VERSION, config('installer.php.version')) >= 0) {
            $passed = true;
        }

        return ['version' => PHP_VERSION, 'passed' => $passed];
    }

    /**
     * Make sure the minimal extensions required are loaded
     * @return array
     */
    public function checkExtensions(): array
    {
        $extensions = [];
        foreach(config('installer.extensions') as $ext) {
            $pass = true;
            if(!extension_loaded($ext)) {
                $pass = false;
            }

            $extensions[] = [
                'ext' => $ext,
                'passed' => $pass,
            ];
        }

        return $extensions;
    }
}
