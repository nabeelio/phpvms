<?php

namespace App\Console\Commands;

use App\Console\BaseCommand;
use Symfony\Component\Yaml\Yaml;

class Version extends BaseCommand
{
    protected $signature = 'phpvms:version {--write}';

    /**
     * Run dev related commands
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public function handle()
    {
        if($this->option('write')) {
            $version_file = config_path('version.yml');

            $cfg = Yaml::parse(file_get_contents($version_file));
            exec($cfg['git']['git-local'], $version);
            $version = substr($version[0], 0, $cfg['build']['length']);
            $cfg['build']['number'] = $version;

            file_put_contents($version_file, Yaml::dump($cfg, 4, 2));
        }

        $this->call('version:show', [
            '--format' => 'compact',
            '--suppress-app-name' => true
        ]);
    }
}
