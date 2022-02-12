<?php
return [
    'environment' => 'local',
    'debug' => true,    
    'timezone' => 'Europe/Berlin',
    'locale' => 'de-DE',
    'boots' => [
        \Tobento\App\Boot\ErrorHandling::class,
    ],
];