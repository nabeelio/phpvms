<?php

return [
    401 => [
        'title'   => 'Unerlaubter Zugriff',
        'message' => 'Sie sind nicht autorisiert diese Aktion auszuführen.' 
            'Klicke <a href=":link">here</a> um zu der Startseite zu kommen.',
    ],
    404 => [
        'title'   => 'Seite nicht gefunden',
        'message' => 'Die angefragte Seite existiert nicht.'.
            'Klicke <a href=":link">here</a> um zu der Startseite zu kommen.',
    ],
    503 => [
        'title'   => 'Interner Error',
        'message' => 'Ein Fehler ist aufgetreten',
    ],
];
