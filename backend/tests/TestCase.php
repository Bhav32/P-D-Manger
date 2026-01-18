<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Get a JWT token for a user
     */
    protected function getJWTToken(User $user = null): string
    {
        if (!$user) {
            $user = User::factory()->create([
                'password' => Hash::make('password')
            ]);
        }

        // Use the JWT guard to attempt login and get token
        $token = Auth::guard('api')->attempt([
            'email' => $user->email,
            'password' => 'password'
        ]);

        if (!$token) {
            // If attempt fails, set the user and refresh
            Auth::guard('api')->setUser($user);
            $token = Auth::guard('api')->refresh();
        }

        return $token;
    }

    /**
     * Make an authenticated request with JWT token
     */
    protected function withJWTToken(User $user = null)
    {
        $token = $this->getJWTToken($user);
        return $this->withHeader('Authorization', "Bearer {$token}");
    }
}
