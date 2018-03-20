<?php

namespace App\Console\Commands;

use App\Console\Command;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Version
 * @package App\Console\Commands
 */
class Version extends Command
{
    protected $signature = 'phpvms:version {--write} {--base-only}';

    /**
     * Create the version number that gets written out
     */
    protected function createVersionNumber($cfg)
    {
        exec($cfg['git']['git-local'], $version);
        $version = substr($version[0], 0, $cfg['build']['length']);

        # prefix with the date in YYMMDD format
        $date = date('ymd');
        $version = $date.'-'.$version;

        return $version;
    }

    /**
     * Run dev related commands
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public function handle()
    {
        $version_file = config_path('version.yml');
        $cfg = Yaml::parse(file_get_contents($version_file));

        # Get the current build id
        $build_number = $this->createVersionNumber($cfg);
        $cfg['build']['number'] = $build_number;

        $c = $cfg['current'];
        $version = "v{$c['major']}.{$c['minor']}.{$c['patch']}-{$build_number}";

        if ($this->option('write')) {
            file_put_contents($version_file, Yaml::dump($cfg, 4, 2));
        }

        # Only show the major.minor.patch version
        if ($this->option('base-only')) {
            $version = 'v'.$cfg['current']['major'].'.'
                .$cfg['current']['minor'].'.'
                .$cfg['current']['patch'];
        }

        print $version."\n";
    }
}
