<?php

namespace App\Support;

use Symfony\Component\ClassLoader\ClassMapGenerator;
use Log;

class ClassLoader
{
    /**
     * @param $path
     * @return array
     */
    public static function getClassesInPath($path)
    {
        if (!file_exists($path)) {
            return [];
        }

        $classes = [];
        $all_classes = array_keys(ClassMapGenerator::createMap($path));
        foreach ($all_classes as $cl) {
            try {
                $klass = new $cl;
            } catch (\Exception $e) {
                Log::error('Error loading class: ' . $e->getMessage());
                continue;
            }

            $classes[] = $klass;
        }

        return $classes;
    }
}
