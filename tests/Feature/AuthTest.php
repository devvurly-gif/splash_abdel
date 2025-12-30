<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful login.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Make login request with session support
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Origin' => 'http://localhost',
            'Referer' => 'http://localhost',
        ])->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            'remember' => false
        ]);

        // Assert successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email'
                ]
            ])
            ->assertJson([
                'message' => 'Login successful',
                'user' => [
                    'email' => 'test@example.com',
                ]
            ]);
    }

    /**
     * Test login with invalid credentials.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        // Create a test user
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Make login request with wrong password
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
            'remember' => false
        ]);

        // Assert validation error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test login validation.
     */
    public function test_login_requires_email_and_password(): void
    {
        // Make login request without credentials
        $response = $this->postJson('/api/login', []);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test logout functionality.
     */
    public function test_authenticated_user_can_logout(): void
    {
        // Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        // Make logout request with session support
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Origin' => 'http://localhost',
            'Referer' => 'http://localhost',
        ])->post('/api/logout');

        // Assert successful logout
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);
    }
}

