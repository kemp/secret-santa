<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AdminBasicAuth
{
    /**
     * Guard the admin area with HTTP Basic auth against the credentials in
     * config/santa.php. There are no user accounts in this app, so this is
     * deliberately simple.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = (string) config('santa.admin.username');
        $password = (string) config('santa.admin.password');

        // No password configured: the admin area doesn't exist.
        abort_if($password === '', 404);

        $givenUsername = (string) $request->getUser();
        $givenPassword = (string) $request->getPassword();

        $passwordMatches = str_starts_with($password, '$2y$')
            ? Hash::check($givenPassword, $password)
            : hash_equals($password, $givenPassword);

        if (! hash_equals($username, $givenUsername) || ! $passwordMatches) {
            return response('Unauthorized.', 401, [
                'WWW-Authenticate' => 'Basic realm="Secret Santa admin", charset="UTF-8"',
            ]);
        }

        return $next($request);
    }
}
