<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User interface theme
    |--------------------------------------------------------------------------
    |
    | The legacy Blade tree remains the source of truth. The modern theme is an
    | override layer and falls back to the legacy view whenever no override is
    | present. This makes rollback a configuration-only operation.
    |
    */
    'theme' => env('UI_THEME', 'legacy'),
    'themes' => [
        'legacy',
        'fleetng-modern',
    ],
];
