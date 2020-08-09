<?php

namespace App\Repositories;

use App\Contracts\Repository;
use App\Models\UserField;

class UserFieldRepository extends Repository
{
    protected $fieldSearchable = [
        'name' => 'like',
    ];

    /**
     * @return string
     */
    public function model(): string
    {
        return UserField::class;
    }
}
