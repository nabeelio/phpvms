<?php

namespace App\Contracts;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Base class for a resource/response
 */
class Resource extends JsonResource
{
    /**
     * Iterate through the list of $fields and check if they're a "Unit"
     * If they are, then add the response
     *
     * @param       $response
     * @param array $fields
     */
    public function checkUnitFields(&$response, array $fields): void
    {
        foreach ($fields as $f) {
            if ($this->{$f} instanceof Unit) {
                $response[$f] = $this->{$f}->getResponseUnits();
            } else {
                $response[$f] = $this->{$f};
            }
        }
    }
}
