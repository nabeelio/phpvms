<?php

namespace App\Console\Commands;

use App\Console\Command;
use App\Services\DatabaseService;
use DB;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlExport
 * @package App\Console\Commands
 */
class YamlExport extends Command
{
    protected $signature = 'phpvms:export {tables*}';
    protected $description = 'YAML table export';

    /**
     * YamlExport constructor.
     * @param DatabaseService $dbSvc
     */
    public function __construct(DatabaseService $dbSvc)
    {
        parent::__construct();
    }

    /**
     * Run dev related commands
     */
    public function handle()
    {
        $tables = $this->argument('tables');
        if (empty($tables)) {
            $this->error('No tables specified');
            exit();
        }

        $export_tables = [];
        foreach ($tables as $table) {
            $export_tables[$table] = [];

            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $export_tables[$table][] = (array) $row;
            }
        }

        $yaml = Yaml::dump($export_tables, 4, 2);
        print($yaml);
    }
}
