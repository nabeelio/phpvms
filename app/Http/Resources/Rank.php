<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Rank extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            #'auto_approve_acars' => $this->auto_approve_acars,
            #'auto_approve_manual' => $this->auto_approve_manual,
            #'auto_promote' => $this->auto_promote,
            #'created_at' => $this->created_at,
            #'updated_at' => $this->updated_at,
        ];
    }
}
