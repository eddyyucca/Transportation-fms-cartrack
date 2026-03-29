<?php

return [
    'base_url' => env('CARTRACK_BASE_URL', 'https://fleetapi-id.cartrack.com'),
    'username' => env('CARTRACK_USERNAME'),
    'password' => env('CARTRACK_PASSWORD'),
    'timeout'  => (int) env('CARTRACK_API_TIMEOUT', 90),
];
