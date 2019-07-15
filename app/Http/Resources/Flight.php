<?php

namespace App\Http\Resources;

use App\Support\Units\Distance;

class Flight extends Response
{
    /**
     * Set the fields on the flight object
     *
     * @return array
     */
    private function setFields()
    {
        $fields = [];
        foreach ($this->field_values as $field) {
            $fields[$field->name] = $field->value;
        }

        return $fields;
    }

    public function toArray($request)
    {
        $res = parent::toArray($request);

        $res['ident'] = $this->ident;

        // Return multiple measures so the client can pick what they want
        $this->checkUnitFields($res, [
            'distance',
        ]);

        $res['airline'] = new Airline($this->airline);
        $res['subfleets'] = Subfleet::collection($this->subfleets);
        $res['fields'] = $this->setFields();

        return $res;
    }
}
