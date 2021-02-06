<?php

return [
    /*
     * Validation Language Lines
     */

    'accepted'   => 'Das :attribute muss akzeptiert sein.',
    'active_url' => 'Das :attribute ist keine valide URL.',
    'after'      => 'Das :attribute muss ein Tag nach dem :date sein.',
    'alpha'      => 'Das :attribute darf nur Buchstaben enthalten.',
    'alpha_dash' => 'Das :attribute darf nur Buchstaben enthalten, Nummern und Striche.',
    'alpha_num'  => 'Das :attribute darf nur Buchstaben enthalten und Nummern.',
    'array'      => 'Das :attribute muss ein Array sein.',
    'before'     => 'Das :attribute muss ein Tag vor dem :date sein.',
    'between'    => [
        'numeric' => 'Das :attribute muss zwischen :min und :max sein.',
        'file'    => 'Das :attribute muss zwischen :min and :max kilobytes sein.',
        'string'  => 'Das :attribute must be between :min and :max characters sein.',
        'array'   => 'Das :attribute muss zwischen :min and :max items sein.',
    ],
    'boolean'        => 'Das :attribute Feld muss true oder false sein.',
    'confirmed'      => 'Die :attribute Bestätigung stimmt nicht überein.',
    'date'           => ':attribute ist kein valider Tag.',
    'date_format'    => 'Das :attribute stimmt nicht mit diesem Format überein :format.',
    'different'      => ':attribute und :other müssen unterschiedlich sein.',
    'digits'         => 'Das :attribute muss :digits beziffern.',
    'digits_between' => 'Das :attribute muss zwischen :min und :max liegen.',
    'dimensions'     => 'Das :attribute hat ungültige Bilddimensionen.',
    'distinct'       => 'Das :attribute Feld hat einen doppelten Wert.',
    'email'          => 'Das :attribute muss eine valide Mail Adresse sein.',
    'exists'         => 'Das ausgewählte :attribute ist invalide.',
    'file'           => 'Das :attribute muss eine Datei sein.',
    'filled'         => 'Das ":attribute" wird benötigt.',
    'image'          => 'Das :attribute muss ein Bild sein.',
    'in'             => 'Das ausgewählte :attribute ist invalide.',
    'in_array'       => 'Das :attribute Feld gibt es nicht in :other.',
    'integer'        => 'Das :attribute muss eine ganze Zahl sein.',
    'ip'             => 'Das :attribute muss eine valide IP Adresse sein.', 
    'json'           => 'Das :attribute muss ein gültiger JSON-String sein.',
    'max'            => [
        'numeric' => 'Das :attribute darf nicht grösser sein als :max.',
        'file'    => 'Das :attribute darf nicht grösser sein als :max kilobytes.',
        'string'  => 'Das :attribute darf nicht grösser sein als :max characters.',
        'array'   => 'Das :attribute darf nicht grösser sein als :max items.',
    ],
    'mimes' => 'Das :attribute muss eine Datei vom Typ sein: :values.',
    'min'   => [
        'numeric' => 'Das :attribute muss mindestens :min.',
        'file'    => 'Das :attribute muss mindestens :min kilobytes.',
        'string'  => 'Das :attribute muss mindestens :min characters.',
        'array'   => 'Das :attribute müssen mindestens :min items.',
    ],
    'not_in'               => 'Das ausgewählte :attribute ist invalide.',
    'numeric'              => 'Das :attribute muss eine Nummer sein.',
    'present'              => 'Das :attribute Feld muss vorhanden sein.',
    'regex'                => 'Das :attribute Format ist invalide.',
    'required'             => 'Das ":attribute" Feld wird benötigt.',
    'required_if'          => 'Das :attribute Feld ist erforderlich, wenn :other is :value.',
    'required_unless'      => 'Das :attribute field is required unless :other is in :values.',
    'required_with'        => 'Das :attribute Feld ist erforderlich, wenn :values ist vorhanden.',
    'required_with_all'    => 'Das :attribute Feld ist erforderlich, wenn :values ist vorhanden.',
    'required_without'     => 'Das :attribute Feld ist erforderlich, wenn :values ist nicht vorhanden.',
    'required_without_all' => 'Das :attribute Feld ist erforderlich, wenn keine :values ist vorhaden sind.',
    'same'                 => 'Das :attribute und :other muss übereinstimmen.',
    'size'                 => [
        'numeric' => 'Das :attribute muss :size groß sein.',
        'file'    => 'Das :attribute muss :size kilobytes sein.',
        'string'  => 'Das :attribute muss :size characters sein.',
        'array'   => 'Das :attribute muss :size items enthalten.',
    ],
    'string'   => 'Das :attribute muss ein String sein.',
    'timezone' => 'Das :attribute muss eine gültige Zone sein.',
    'unique'   => 'Das :attribute wurde bereits genommen.',
    'url'      => 'Das :attribute Format ist invalide.',

    /*
     * Custom Validation Language Lines
     */

    'custom' => [
        'airline_id' => [
            'required' => 'Eine Airline wird benötigt',
            'exists'   => 'Diese Airline gibt es nicht',
        ],
        'aircraft_id' => [
            'required' => 'Ein Flugzeug wird benötigt',
            'exists'   => 'Dieses Flugzeug gibt es nicht',
        ],
        'arr_airport_id' => [
            'required' => 'Ein Ankunftsflughafen ist erforderlich',
        ],
        'dpt_airport_id' => [
            'required' => 'Ein Abflughafen ist erforderlich',
        ],
        'flight_time' => [
            'required' => 'Flug Zeit, in Minuten, wird benötigt',
            'integer'  => 'Flug Zeit, in Minuten, wird benötigt',
        ],
        'planned_flight_time' => [
            'required' => 'Flug Zeit, in Minuten, wird benötigt',
            'integer'  => 'Flug Zeit, in Minuten, wird benötigt',
        ],
        'source_name' => [
            'required' => 'PIREP Quelle wird benötigt',
        ],
        'g-recaptcha-response' => [
            'required' => 'Bitte bestätigen Sie, dass Sie kein Roboter sind.',
            'captcha'  => 'Captcha Fehler! Bitte später nochmal versuchen und sonst ein Staff Mitglied kontaktieren.',
        ],
    ],

    /*
     * Custom Validation Attributes
     */

    'attributes' => [],

];
