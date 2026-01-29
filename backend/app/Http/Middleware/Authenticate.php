<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, $next, ...$guards)
    {
        try {
            return parent::handle($request, $next, ...$guards);
        } catch (TokenExpiredException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Token expired, please login again'], 401);
            }
            return redirect()->route('login');
        } catch (JWTException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid token'], 401);
            }
            return redirect()->route('login');
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
