<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class PirepFieldCollection
 * @package App\Http\Resources
 */
class PirepFieldCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $obj = [];
        foreach($this->collection as $field) {
            $obj[$field->name] = $field->value;
        }

        return $obj;
    }
}
