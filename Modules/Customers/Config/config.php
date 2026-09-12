<?php

return [
    'name' => 'Customers',
    'payments_enabled' => env('CUSTOMER_PAYMENTS_ENABLED', false),
    'profile_disk' => env('CUSTOMER_PROFILE_DISK', env('APP_ENV') === 'production' ? 's3' : 'local'),
    'front_url' => env('CUSTOMER_PORTAL_FRONTEND_URL', 'https://staging.fleetng.com'),
];
