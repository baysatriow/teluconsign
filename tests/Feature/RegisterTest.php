<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{

    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->mock(\App\Services\FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'testuser@gmail.com',
            'phone' => '081234567890',
            'password' => 'Password@123', 
            'password_confirmation' => 'Password@123',
            'terms' => true,
        ]);

        $response->assertRedirect(route('otp.verify'));
        $this->assertDatabaseHas('users', ['email' => 'testuser@gmail.com']);
    }

    public function test_registration_fails_with_invalid_data(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }
}
