<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class AircraftTest extends TestCase
{

    protected $repo;

    public function setUp() {
        parent::setUp();
        $this->repo = $this->createRepository('AircraftRepository');
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRepository()
    {
        print_r($this->repo->model());
    }
}
