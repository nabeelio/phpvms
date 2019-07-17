<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class Response
 */
class PirepComment extends Resource
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
        $user = $this->user;

        return [
            'id'         => $this->id,
            'comment'    => $this->comment,
            'created_at' => $this->created_at,
            'user'       => [
                'id'       => $user->id,
                'pilot_id' => $user->pilot_id,
                'ident'    => $user->ident,
                'name'     => $user->name,
            ],
        ];
    }
}
