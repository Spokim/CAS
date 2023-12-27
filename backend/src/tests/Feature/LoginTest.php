<?php

namespace Tests\Feature;

use Database\Factories\Authorized_usersFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_authorized_user_can_log_in()
    {
        $user = UserFactory::new()->withPrivileges()->create();

        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout');
    }

    public function test_authorized_user_can_not_log_in()
    {
        $user = UserFactory::new()->withoutPrivileges()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/unauthorized');
        $this->assertGuest();
    }
}
