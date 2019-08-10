<?php

namespace App\Console\Commands;

use App\Console\Command;
use DB;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlExport
 */
class YamlExport extends Command
{
    protected $signature = 'phpvms:yaml-export {tables*}';
    protected $description = 'YAML table export';

    public function __construct()
    {
        parent::__construct();

        $this->redirectLoggingToFile('stdout');
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
        echo $yaml;
    }
}
