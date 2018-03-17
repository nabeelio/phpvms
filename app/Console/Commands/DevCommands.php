<?php

namespace App\Console\Commands;

use App\Console\BaseCommand;
use App\Models\Acars;
use App\Models\Airline;
use App\Models\Pirep;
use App\Models\User;
use App\Services\AwardsService;
use DB;
use PDO;
use Symfony\Component\Yaml\Yaml;

class DevCommands extends BaseCommand
{
    protected $signature = 'phpvms {cmd} {param?}';
    protected $description = 'Developer commands';

    /**
     * Run dev related commands
     */
    public function handle()
    {
        $command = trim($this->argument('cmd'));

        if (!$command) {
            $this->error('No command specified!');
            exit();
        }

        $commands = [
            'list-awards'     => 'listAwardClasses',
            'clear-acars'     => 'clearAcars',
            'clear-users'     => 'clearUsers',
            'compile-assets'  => 'compileAssets',
            'db-attrs'        => 'dbAttrs',
            'xml-to-yaml'     => 'xmlToYaml',
        ];

        if(!array_key_exists($command, $commands)) {
            $this->error('Command not found!');
            exit();
        }

        $this->{$commands[$command]}();
    }

    /**
     * List all award classes
     */
    protected function listAwardClasses()
    {
        $awardSvc = app(AwardsService::class);
        $awards = $awardSvc->findAllAwardClasses();

        $headers = ['Award Name', 'Class'];
        $formatted_awards = [];
        foreach($awards as $award) {
            $formatted_awards[] = [$award->name, \get_class($award)];
        }

        $this->table($headers, $formatted_awards);
    }

    /**
     * Delete all the data from the ACARS and PIREP tables
     */
    protected function clearAcars()
    {
        if(config('database.default') === 'mysql') {
            DB::statement('SET foreign_key_checks=0');
        }

        Acars::truncate();
        Pirep::truncate();

        if (config('database.default') === 'mysql') {
            DB::statement('SET foreign_key_checks=1');
        }

        $this->info('ACARS and PIREPs cleared!');
    }

    /**
     * Delete all the data from the ACARS and PIREP tables
     */
    protected function clearUsers()
    {
        if (config('database.default') === 'mysql') {
            DB::statement('SET foreign_key_checks=0');
        }

        DB::statement('TRUNCATE `role_user`');
        Airline::truncate();
        User::truncate();

        if (config('database.default') === 'mysql') {
            DB::statement('SET foreign_key_checks=1');
        }

        $this->info('Users cleared!');
    }

    /**
     * Compile all the CSS/JS assets into their respective files
     * Calling the webpack compiler
     */
    protected function compileAssets()
    {
        $this->runCommand('npm update');
        $this->runCommand('npm run dev');
    }

    /**
     * Output DB prepares versions
     */
    protected function dbAttrs()
    {
        $pdo = DB::connection()->getPdo();
        $emulate_prepares_below_version = '5.1.17';
        $server_version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
        $emulate_prepares = version_compare($server_version, $emulate_prepares_below_version, '<');

        $this->info('Server Version: '. $server_version);
        $this->info('Emulate Prepares: '.$emulate_prepares);

        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $emulate_prepares);
    }

    /**
     * Convert the sequelpro xml export to yaml
     */
    protected function xmlToYaml()
    {
        $file = $this->argument('param');
        $this->info('Reading '. $file);

        $xml_str = file_get_contents($file);
        $xml = new \SimpleXMLElement($xml_str);

        $yaml = [];
        $table_name = (string) $xml->database->table_data['name'];
        $this->info('Writing table "'.$table_name.'"');

        $count = 0;
        $yaml[$table_name] = [];

        foreach ($xml->database->table_data->row as $row) {
            $yaml_row = [];
            foreach($row->field as $field) {
                $fname = (string) $field['name'];
                $fvalue = (string) $field;

                $yaml_row[$fname] = $fvalue;
            }

            $yaml[$table_name][] = $yaml_row;
            ++$count;
        }

        $this->info('Exporting '.$count.' rows');

        $file_name = $table_name.'.yml';
        file_put_contents(storage_path($file_name), Yaml::dump($yaml, 4, 2));
        $this->info('Writing yaml to storage: '. $file_name);
    }
}
