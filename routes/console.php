<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;

Artisan::command('santa:hash-password', function () {
    $plain = password('Admin password', required: true);

    $this->line('Add this to your .env file:');
    $this->newLine();
    $this->line('ADMIN_PASSWORD="'.Hash::make($plain).'"');
})->purpose('Generate a bcrypt hash for ADMIN_PASSWORD');
