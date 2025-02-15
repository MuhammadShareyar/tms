<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_login_validation(): void
    {
        $this->postJson('api/login')
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "email" => [
                        "The email field is required."
                    ],
                    "password" => [
                        "The password field is required.",
                    ]
                ]
            ]);
    }


    public function test_user_login_successful()
    {
        $user = User::factory(1)->create()->first();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['body' => ['token']]);
    }
}
