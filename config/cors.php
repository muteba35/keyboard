<?php

return [

    'paths' => ['*'], // Active CORS sur toutes les routes

    'allowed_methods' => ['*'], // Toutes les méthodes HTTP

    // Autorise toutes les origines (utile pour extension Chrome ou développement)
    'allowed_origins' => ['*'],

    // Si tu veux plus de contrôle, tu peux mettre :
    // 'allowed_origins_patterns' => ['chrome-extension://.*'],

    'allowed_headers' => ['*'], // Autorise tous les headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, // Si tu veux autoriser les cookies, mets true
];