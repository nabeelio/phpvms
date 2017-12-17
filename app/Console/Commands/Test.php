<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class Test extends Command
{
    protected $signature = 'phpvms:test';
    protected $description = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        print resource_path();
    }
}
