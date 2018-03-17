<?php

namespace App\Services;

use App\Support\ClassLoader;
use Module;

class AwardService
{
    /**
     * Find any of the award classes
     * @return \App\Interfaces\AwardInterface[]
     */
    public function findAllAwardClasses()
    {
        $awards = [];
        $formatted_awards = [];

        # Find the awards in the app/Awards directory
        $classes = ClassLoader::getClassesInPath(app_path('/Awards'));
        $awards = array_merge($awards, $classes);

        # Look throughout all the other modules, in the module/{MODULE}/Awards directory
        foreach (Module::all() as $module) {
            $path = $module->getExtraPath('Awards');
            $classes = ClassLoader::getClassesInPath($path);
            $awards = array_merge($awards, $classes);
        }

        foreach ($awards as $award) {
            $formatted_awards[\get_class($award)] = $award;
        }

        return $formatted_awards;
    }
}
