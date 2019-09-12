<?php

namespace App\Console\Commands;

use App\Console\Command;
use App\Services\VersionService;
use Symfony\Component\Yaml\Yaml;

class Version extends Command
{
    protected $signature = 'phpvms:version {--write} {--base-only}';

    private $versionSvc;

    public function __construct(VersionService $versionSvc)
    {
        parent::__construct();

        $this->versionSvc = $versionSvc;
    }

    /**
     * Run dev related commands
     *
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public function handle()
    {
        // Write the updated build number out to the file
        if ($this->option('write')) {
            $version_file = config_path('version.yml');
            $cfg = Yaml::parse(file_get_contents($version_file));
            $build_number = $this->versionSvc->getBuildId($cfg);
            $cfg['build']['number'] = $build_number;

            file_put_contents($version_file, Yaml::dump($cfg, 4, 2));
        }

        $version = $this->versionSvc->getCurrentVersion(!$this->option('base-only'));
        echo $version."\n";
    }
}
