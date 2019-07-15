<?php

namespace App\Http\Resources;

class JournalTransaction extends Response
{
    public function toArray($request)
    {
        $transaction = parent::toArray($request);
        return $transaction;
    }
}
