<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // Added '*' to paths to ensure all routes are accessible during development
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],

    'allowed_methods' => ['*'],

    // Ensure this exactly matches the URL in your browser when running Next.js
    'allowed_origins' => ['http://localhost:3000'], 

    'allowed_origins_patterns' => [],

    // Allowing all headers is best for Axios compatibility
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Must be true to allow Sanctum to send cookies/tokens back and forth
    'supports_credentials' => true, 

];