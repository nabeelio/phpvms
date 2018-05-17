<?php

return [

    'global' => [
        'active'   => 'Attivo',
        'inactive' => 'Inattivo'
    ],

    'aircraft' => [
        'status' => [
            'active'   => 'Attivo',
            'stored'   => 'Immagazzinato',
            'retired'  => 'Ritirato',
            'scrapped' => 'Rottamato',
            'written'  => 'Stornato',
        ],
    ],

    'days' => [
        'mon'   => 'Lunedì',
        'tues'  => 'Martedì',
        'wed'   => 'Mercoledì',
        'thurs' => 'Giovedì',
        'fri'   => 'Venerdì',
        'sat'   => 'Sabato',
        'sun'   => 'Domenica',
    ],

    'expenses' => [
        'type' => [
            'flight'  => 'Volo',
            'daily'   => 'Giornaliera',
            'monthly' => 'Mensile',
        ],
    ],

    'flights' => [
        'type' => [
            'pass_scheduled'    => 'Passeggeri - Programmato',
            'cargo_scheduled'   => 'Cargo - Programmato',
            'charter_pass_only' => 'Charter - Solo Passeggeri',
            'addtl_cargo_mail'  => 'Cargo/Posta Addizionale',
            'special_vip'       => 'Volo VIP Speciale (FAA/Governo)',
            'pass_addtl'        => 'Passeggeri - Addizionale',
            'charter_cargo'     => 'Charter - Cargo/Posta',
            'ambulance'         => 'Volo Ambulanza',
            'training_flight'   => 'Volo di Addestramento',
            'mail_service'      => 'Servizio Postale',
            'charter_special'   => 'Charter con Manutenzione Speciale',
            'positioning'       => 'Posizionamento (Traghetto/Consegna/Dimostrazione)',
            'technical_test'    => 'Prova Tecnica',
            'military'          => 'Militare',
            'technical_stop'    => 'Fermo Tecnico',
        ],
    ],

    'pireps' => [
        'source' => [
            'manual' => 'Manuale',
            'acars'  => 'ACARS',
        ],
        'state' => [
            'accepted'    => 'Accettato',
            'pending'     => 'In Attesa di Approvazione',
            'rejected'    => 'Rifiutato',
            'in_progress' => 'In Lavorazione',
            'cancelled'   => 'Cancellato',
            'deleted'     => 'Eliminato',
            'draft'       => 'Bozza',
        ],
        'status' => [
            'initialized'  => 'Iniziato',
            'scheduled'    => 'Programmato',
            'boarding'     => 'Imbarco',
            'ready_start'  => 'Pronto alla partenza',
            'push_tow'     => 'Pushback/Rimorchio',
            'departed'     => 'Partito',
            'ready_deice'  => 'Pronto al de-icing',
            'deicing'      => 'De-icing in corso',
            'ground_ret'   => 'Ritorno a Terra',
            'taxi'         => 'Taxi',
            'takeoff'      => 'Decollo',
            'initial_clb'  => 'Salita Iniziale',
            'enroute'      => 'Enroute',
            'diverted'     => 'Diverted',
            'approach'     => 'Approccio',
            'final_appr'   => 'Approccio Finale',
            'landing'      => 'Atterraggio',
            'landed'       => 'Atterrato',
            'arrived'      => 'Arrivato',
            'cancelled'    => 'Cancellato',
            'emerg_decent' => 'Discesa di Emergenza',
        ]
    ],

    'users' => [
        'state' => [
            'pending'   => 'In Attesa',
            'active'    => 'Attivo',
            'rejected'  => 'Rifiutato',
            'on_leave'  => 'In Ferie',
            'suspended' => 'Sospeso',
        ],
    ],
];
