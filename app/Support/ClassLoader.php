<?php

namespace App\Support;

use Symfony\Component\ClassLoader\ClassMapGenerator;

class ClassLoader
{
    /**
     * @param $path
     * @return array
     */
    public static function getClassesInPath($path)
    {
        $classes = [];

        if (!file_exists($path)) {
            return [];
        }

        $all_classes = array_keys(ClassMapGenerator::createMap($path));
        foreach ($all_classes as $cl) {
            $klass = new $cl;
            $classes[] = $klass;
        }

        return $classes;
    }
}
