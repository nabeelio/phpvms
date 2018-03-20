<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class JournalTransaction extends Resource
{
    public function toArray($request)
    {
        $transaction = parent::toArray($request);

        return $transaction;
    }
}
