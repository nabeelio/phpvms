<?php

namespace App\Http\Resources;

class Acars extends Response
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $res = parent::toArray($request);

        $this->checkUnitFields($res, [
            'distance',
            'fuel',
        ]);

        return $res;
    }
}
