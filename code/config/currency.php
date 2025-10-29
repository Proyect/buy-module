<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Moneda
    |--------------------------------------------------------------------------
    |
    | Esta configuración define la moneda principal del sistema y sus
    | características de formato para la aplicación.
    |
    */

    'default' => 'ARS',

    'currencies' => [
        'ARS' => [
            'name' => 'Peso Argentino',
            'symbol' => '$',
            'code' => 'ARS',
            'decimal_places' => 2,
            'thousands_separator' => '.',
            'decimal_separator' => ',',
            'symbol_position' => 'before', // 'before' o 'after'
            'format' => '{symbol} {amount} {code}',
        ],
        'USD' => [
            'name' => 'Dólar Estadounidense',
            'symbol' => 'US$',
            'code' => 'USD',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'format' => '{symbol} {amount}',
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'code' => 'EUR',
            'decimal_places' => 2,
            'thousands_separator' => '.',
            'decimal_separator' => ',',
            'symbol_position' => 'after',
            'format' => '{amount} {symbol}',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Formato de Moneda por Defecto
    |--------------------------------------------------------------------------
    |
    | Define el formato por defecto para mostrar cantidades monetarias
    | en toda la aplicación.
    |
    */

    'format' => [
        'locale' => 'es_AR', // Locale argentino
        'currency' => 'ARS',
        'symbol' => '$',
        'decimal_places' => 2,
        'thousands_separator' => '.',
        'decimal_separator' => ',',
    ],
];


