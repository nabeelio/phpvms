<?php

namespace App\Console\Commands;

use App\Contracts\Command;
use App\Services\Installer\ConfigService;
use PDO;

/**
 * Command to rewrite the config files
 *
 * @package App\Console\Commands
 */
class RewriteConfigs extends Command
{
    protected $signature = 'phpvms:rewrite-configs';
    protected $description = 'Rewrite the config files';

    /**
     * Run dev related commands
     */
    public function handle()
    {
        /** @var ConfigService $configSvc */
        $configSvc = app(ConfigService::class);
        $configSvc->rewriteConfigFiles();
    }
}
