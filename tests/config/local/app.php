<?php
return [
    'environment' => 'local',
    'debug' => true,    
    'timezone' => 'Europe/Berlin',
    'locale' => 'en-US',
    'boots' => [
        \Tobento\App\Boot\ErrorHandling::class,
    ],
];