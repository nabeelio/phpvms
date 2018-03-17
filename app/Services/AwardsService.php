<?php

namespace App\Services;

use Module;
use Symfony\Component\ClassLoader\ClassMapGenerator;

class AwardsService
{
    /**
     * Find any of the award classes
     */
    public function findAllAwardClasses()
    {
        $awards = [];
        foreach (Module::all() as $module) {
            $path = $module->getExtraPath('Awards');
            if(!file_exists($path)) {
                continue;
            }

            $classes = array_keys(ClassMapGenerator::createMap($path));
            foreach($classes as $cl) {
                $klass = new $cl;
                $awards[] = $klass;
            }
        }

        return $awards;
    }
}
