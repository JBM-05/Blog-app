<?php

namespace App\Lib;

use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class util
{
    /**
     * Creates an HTTP-only, secure cookie for API token storage.
     *
     * @param string $token The plaintext Sanctum token.
     * @param int $expirationMinutes The number of minutes until expiration.
     * @param string $sameSite The SameSite attribute (e.g., 'Lax', 'Strict', 'None').
     * @return SymfonyCookie
     */
    public static function create(string $token, int $expirationMinutes, string $sameSite = 'Lax'): SymfonyCookie
    {
        // Adjust the SameSite default for security if not specified
        if ($sameSite === 'none') {
             // 'None' requires the 'Secure' flag to be true
             $sameSite = 'None';
        }

        // The parameters match the cookie() helper in order:
        // name, value, minutes, path, domain, secure, httpOnly, raw, sameSite
        return Cookie::make(
            'api_token',
            $token,
            $expirationMinutes,
            '/',
            null,
            config('app.env') !== 'local', // Secure: true if not local (forces HTTPS)
            true, // HttpOnly: TRUE (prevents JS access)
            false,
            $sameSite
        );
    }

    /**
     * Creates an expired cookie to force deletion on the client-side.
     *
     * @return SymfonyCookie
     */
    public static function forget(): SymfonyCookie
    {
        // Set the same cookie name with a negative time (-1) to force deletion
        return Cookie::make(
            'api_token',
            null,
            -1,
            '/',
            null,
            config('app.env') !== 'local',
            true,
            false,
            'Lax'
        );
    }
}
