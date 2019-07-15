<?php

namespace App\Http\Resources;

use App\Interfaces\Unit;
use Illuminate\Http\Resources\Json\Resource;

/**
 * Class Response
 */
class Response extends Resource
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
