<?php

return [

    'global' => [
        'active'   => 'Activo',
        'inactive' => 'Inactivo'
    ],

    'aircraft' => [
        'status' => [
            'active'   => 'Activo',
            'stored'   => 'Guardado',
            'retired'  => 'Retirado',
            'scrapped' => 'Desguazado',
            'written'  => 'Dado de baja',
        ],
    ],

    'days' => [
        'mon'   => 'lunes',
        'tues'  => 'martes',
        'wed'   => 'miércoles',
        'thurs' => 'jueves',
        'fri'   => 'viernes',
        'sat'   => 'sábado',
        'sun'   => 'domingo',
    ],

    'expenses' => [
        'type' => [
            'flight'  => 'Por vuelo',
            'daily'   => 'Diario',
            'monthly' => 'Mensual',
        ],
    ],

    'flights' => [
        'type' => [
            'pass_scheduled'    => 'Pasajero - Programado',
            'cargo_scheduled'   => 'Carga - Programado',
            'charter_pass_only' => 'Charter - Pasajeros únicamente',
            'addtl_cargo_mail'  => 'Carga/Correo adicional',
            'special_vip'       => 'Vuelo VIP especial (Autoridad de Aviación Civil)',
            'pass_addtl'        => 'Pasajero - Adicional',
            'charter_cargo'     => 'Charter - Carga/Correo',
            'ambulance'         => 'Vuelo ambulancia',
            'training_flight'   => 'Vuelo de entrenamiento',
            'mail_service'      => 'Servicio postal',
            'charter_special'   => 'Charter con cargas especiales',
            'positioning'       => 'Posicionamiento (Ferry/Entrega/Demo)',
            'technical_test'    => 'Prueba técnica',
            'military'          => 'Militar',
            'technical_stop'    => 'Parada técnica',
        ],
    ],

    'pireps' => [
        'source' => [
            'manual' => 'Manual',
            'acars'  => 'ACARS',
        ],
        'state' => [
            'accepted'    => 'Aceptado',
            'pending'     => 'Pendiente',
            'rejected'    => 'Rechazado',
            'in_progress' => 'En progreso',
            'cancelled'   => 'Cancelado',
            'deleted'     => 'Borrado',
            'draft'       => 'Borrador',
        ],
        'status' => [
            'initialized'  => 'Inicializado',
            'scheduled'    => 'Programado',
            'boarding'     => 'Embarcando',
            'ready_start'  => 'Listo para empezar',
            'push_tow'     => 'Pushback/remolcado',
            'departed'     => 'Salió',
            'ready_deice'  => 'Listo para deshielo',
            'deicing'      => 'Deeshielo en progreso',
            'ground_ret'   => 'Retorno a tierra',
            'taxi'         => 'Taxi',
            'takeoff'      => 'Despegue',
            'initial_clb'  => 'Ascenso inicial',
            'enroute'      => 'En ruta',
            'diverted'     => 'Desviado',
            'approach'     => 'En aproximación',
            'final_appr'   => 'En aproximación final',
            'landing'      => 'Aterrizando',
            'landed'       => 'En tierra',
            'arrived'      => 'Llegó',
            'cancelled'    => 'Cancelado',
            'emerg_decent' => 'Descenso de emergencia',
        ]
    ],

    'users' => [
        'state' => [
            'pending'   => 'Pendiente',
            'active'    => 'Activo',
            'rejected'  => 'Rechazado',
            'on_leave'  => 'De vacaciones',
            'suspended' => 'Suspendido',
        ],
    ],
];
