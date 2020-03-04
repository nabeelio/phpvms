<?php

namespace App\Http\Resources;

use App\Support\Units\Distance;
use stdClass;

class Flight extends Response
{
    /**
     * Set the fields on the flight object
     *
     * @mixin \App\Models\Flight
     */
    private function setFields()
    {
        /** @var \Illuminate\Support\Collection $field_values */
        $field_values = $this->field_values;
        if (empty($field_values) || $field_values->count() === 0) {
            return new stdClass();
        }

        $fields = [];
        foreach ($field_values as $field) {
            $fields[$field->name] = $field->value;
        }

        return $fields;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     *
     * @return array
     */
    public function toArray($request)
    {
        $res = parent::toArray($request);

        $res['ident'] = $this->ident;

        $distance = new Distance($res['distance'], config('phpvms.internal_units.distance'));
        $res['distance'] = $distance->getResponseUnits();

        $res['airline'] = new Airline($this->airline);
        $res['subfleets'] = Subfleet::collection($this->subfleets);
        $res['fields'] = $this->setFields();

        return $res;
    }
}
