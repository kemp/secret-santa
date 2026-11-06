<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Credentials
    |--------------------------------------------------------------------------
    |
    | The question admin at /admin is protected with HTTP Basic auth. Leave
    | the password empty to switch the admin area off entirely. The password
    | may be plain text or a bcrypt hash (php artisan santa:hash-password).
    |
    */

    'admin' => [
        'username' => env('ADMIN_USERNAME', 'admin'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];
