<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_error_for_email_and_password()
    {
        $response = $this->postJson('/api/login', []);
        $response->assertUnprocessable()
            ->assertInvalid('email')
            ->assertInvalid('password');
    }

    public function test_unAutinticated_user()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'WrongPassword']);
        $response->assertUnauthorized();
    }
    public function test_Autinticated_user_andToken_returned()
    {
        $user = User::factory()->create(['password' => '123456']);

        $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => '123456']);
        $response->assertok()
            ->assertJsonStructure(['token']);
    }
}
