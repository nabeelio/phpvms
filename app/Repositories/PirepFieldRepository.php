<?php

namespace App\Repositories;

use App\Models\PirepField;

class PirepFieldRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name' => 'like',
    ];

    public function model()
    {
        return PirepField::class;
    }
}
