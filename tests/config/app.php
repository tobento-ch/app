<?php
return [
    'environment' => 'production',
    'debug' => true,    
    'timezone' => 'Europe/Berlin',
    'locale' => 'de-DE',
    'boots' => [
        \Tobento\App\Boot\ErrorHandling::class,
    ],
];