<?php

namespace App\Services;

use App\Interfaces\Service;
use App\Support\ClassLoader;
use Module;

/**
 * Class AwardService
 */
class AwardService extends Service
{
    /**
     * Find any of the award classes
     *
     * @return \App\Interfaces\Award[]
     */
    public function findAllAwardClasses(): array
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
