<?php

return [
    /*
     * Validation Language Lines
     */

    'accepted'   => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after'      => 'The :attribute must be a date after :date.',
    'alpha'      => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'  => 'The :attribute may only contain letters and numbers.',
    'array'      => 'The :attribute must be an array.',
    'before'     => 'The :attribute must be a date before :date.',
    'between'    => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'        => 'The :attribute field must be true or false.',
    'confirmed'      => 'The :attribute confirmation does not match.',
    'date'           => 'The :attribute is not a valid date.',
    'date_format'    => 'The :attribute does not match the format :format.',
    'different'      => 'The :attribute and :other must be different.',
    'digits'         => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions'     => 'The :attribute has invalid image dimensions.',
    'distinct'       => 'The :attribute field has a duplicate value.',
    'email'          => 'The :attribute must be a valid email address.',
    'exists'         => 'The selected :attribute is invalid.',
    'file'           => 'The :attribute must be a file.',
    'filled'         => 'The ":attribute" is required.',
    'image'          => 'The :attribute must be an image.',
    'in'             => 'The selected :attribute is invalid.',
    'in_array'       => 'The :attribute field does not exist in :other.',
    'integer'        => 'The :attribute must be an integer.',
    'ip'             => 'The :attribute must be a valid IP address.',
    'json'           => 'The :attribute must be a valid JSON string.',
    'max'            => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'min'   => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The ":attribute" field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'   => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique'   => 'The :attribute has already been taken.',
    'url'      => 'The :attribute format is invalid.',

    /*
     * Custom Validation Language Lines
     */

    'custom' => [
        'airline_id' => [
            'required' => 'Eine Fluggesellschaft ist erforderlich',
            'exists'   => 'Die Fluggesellschaft gibt es nicht',
        ],
        'aircraft_id' => [
            'required' => 'Ein Flugzeug ist erforderlich',
            'exists'   => 'Das Flugzeug gibt es nicht',
        ],
        'arr_airport_id' => [
            'required' => 'Ein Ankunftsflughafen ist erforderlich',
        ],
        'dpt_airport_id' => [
            'required' => 'Ein Abflughafen ist erforderlich',
        ],
        'flight_time' => [
            'required' => 'Flugzeit, in Minuten, ist erforderlich',
            'integer'  => 'Flugzeit, in Minuten, ist erforderlich',
        ],
        'planned_flight_time' => [
            'required' => 'Flugzeit, in Minuten, ist erforderlich',
            'integer'  => 'Flugzeit, in Minuten, ist erforderlich',
        ],
        'source_name' => [
            'required' => 'PIREP Quelle ist erforderlich',
        ],
        'g-recaptcha-response' => [
            'required' => 'Bitte verifiziere, dass du kein Roboter sind.',
            'captcha'  => 'Captcha-Fehler! Versuche es später noch einmal oder wende dich an den Seiten-Administrator.',
        ],
    ],

    /*
     * Custom Validation Attributes
     */

    'attributes' => [],

];
