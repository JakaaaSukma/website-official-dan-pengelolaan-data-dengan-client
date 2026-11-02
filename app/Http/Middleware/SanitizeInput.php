<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sanitize all text input to prevent XSS attacks.
 * Strips potentially dangerous HTML tags from all input fields,
 * while preserving safe formatting characters like newlines.
 */
class SanitizeInput
{
    /**
     * Fields that should NOT be sanitized (e.g., password fields).
     */
    protected array $except = [
        'password',
        'current_password',
        'new_password',
        'password_confirmation',
        'new_password_confirmation',
        'current_password_email',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        $sanitized = $this->sanitize($input);
        $request->merge($sanitized);

        return $next($request);
    }

    /**
     * Recursively sanitize input values.
     */
    protected function sanitize(array $input): array
    {
        foreach ($input as $key => $value) {
            if (in_array($key, $this->except, true)) {
                continue;
            }

            if (is_array($value)) {
                $input[$key] = $this->sanitize($value);
            } elseif (is_string($value)) {
                // Strip all HTML tags except safe ones, then trim
                $input[$key] = strip_tags($value);
            }
        }

        return $input;
    }
}
