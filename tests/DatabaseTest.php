<?php

namespace Tests;

use App\Services\DatabaseService;
use App\Support\Database;
use Symfony\Component\Yaml\Yaml;

class DatabaseTest extends TestCase
{
    /**
     * Make sure the seeder works correctly
     */
    public function testSeeder()
    {
        /** @var DatabaseService $dbSvc */
        $file = file_get_contents(base_path('tests/data/seed.yml'));
        $yml = Yaml::parse($file);

        Database::seed_from_yaml($yml);
        $value = setting('test.setting');
        $this->assertEquals('default', $value);

        // Try updating the value now
        $yml['settings']['ignore_on_update'] = [];
        $yml['settings']['data'][0]['value'] = 'changed';

        Database::seed_from_yaml($yml);
        $value = setting('test.setting');
        $this->assertEquals('changed', $value);
    }
}
