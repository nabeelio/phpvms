<?php

namespace App\Http\Resources;

use App\Contracts\Resource;

/**
 * @mixin \App\Models\Aircraft
 */
class Aircraft extends Resource
{
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $res['ident'] = $this->ident;

        return $res;
    }
}
