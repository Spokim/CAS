<?php

namespace Tests\Feature;

use Database\Factories\Authorized_usersFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    public function test_users_can_register() {
        $data = [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $data);
        $response->assertStatus(302);
        $this->assertDatabaseHas('users', ['email' => $data['email']]);
    }

    public function test_already_authorized_users_get_logged_in_and_redirected_home() {
        $data = [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $data['email'],
        ]);

        $response = $this->post('/register', $data);

        $response->assertStatus(302);
        $response->assertRedirect('/home');
        $this->assertAuthenticated();
        $this->assertEquals($data['email'], auth()->user()->email);
    }

    public function test_unauthorized_users_get_redirected_to_confirmation_page_and_logged_out() {
        $data = [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $data);

        $response->assertStatus(302);
        $response->assertRedirect('/confirmation');
        $this->assertGuest();
    }
}
