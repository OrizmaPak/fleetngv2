<?php

return [
    'sms_enabled' => env('SMS_ENABLED', false),
    'sms_key' => env('TERMII_API_KEY'),
    'image_disk' => env('STAFF_IMAGE_DISK', env('APP_ENV') === 'production' ? 's3' : 'local'),
    'tracking_enabled' => env('TRACKING_ENABLED', false),
    'tracking_username' => env('TRACKING_USERNAME'),
    'tracking_key' => env('TRACKING_KEY'),
    'google_maps_key' => env('GOOGLE_MAPS_KEY', 'AIzaSyDU6bmt7uOJ1WPpcveuiTjdOdf04w1zi_U'),
];
