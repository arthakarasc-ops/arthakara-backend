<?php

return [
    'client_id' => env('DOKU_CLIENT_ID'),
    'shared_key' => env('DOKU_SHARED_KEY'),
    'is_production' => env('DOKU_IS_PRODUCTION', false),
    'api_url' => env('DOKU_IS_PRODUCTION', false)
        ? 'https://api.doku.com'
        : 'https://api-sandbox.doku.com',
];
