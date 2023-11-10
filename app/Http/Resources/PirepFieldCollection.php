<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PirepFieldCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $res = [];
        foreach ($this->collection as $field) {
            if ($field->manual_only == 1) {
                continue;
            }
            $res[$field->name] = $field->value;
        }

        return $res;
    }
}
