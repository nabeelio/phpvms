<?php

namespace App\Services;

use App\Support\ClassLoader;
use Module;

class AwardsService
{
    /**
     * Find any of the award classes
     */
    public function findAllAwardClasses()
    {
        $awards = [];

        # Find the awards in the app/Awards directory
        $classes = ClassLoader::getClassesInPath(app_path('/Awards'));
        $awards = array_merge($awards, $classes);

        # Look throughout all the other modules
        foreach (Module::all() as $module) {
            $path = $module->getExtraPath('Awards');
            $classes = ClassLoader::getClassesInPath($path);
            $awards = array_merge($awards, $classes);
        }

        return $awards;
    }
}
