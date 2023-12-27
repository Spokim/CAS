<?php

namespace Tests\Feature;

use Database\Factories\Authorized_usersFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_normal_user_nav_links() {
        $user = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)->get('/home');
        $response->assertSee('Home');
        $response->assertSee('Report Work Shift');
        $response->assertSee('Past Work Shift');

        $response1 = $this->actingAs($user)->get('/work-shift');
        $response1->assertSee('Home');
        $response1->assertSee('Report Work Shift');
        $response1->assertSee('Past Work Shift');

        $response2 = $this->actingAs($user)->get('/past-work-shift');
        $response2->assertSee('Home');
        $response2->assertSee('Report Work Shift');
        $response2->assertSee('Past Work Shift');
    }

    public function test_normal_user_cannot_see_supervisor_or_admin_nav_links() {
        $user = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)->get('/home');
        $response->assertDontSee('Create News');
        $response->assertDontSee('Supervisor');
        $response->assertDontSee('Admin');
    }

    public function test_normal_user_cannot_enter_supervisor_or_admin_links() {
        $user = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)->get('/create-news');
        $response->assertStatus(403);

        $response1 = $this->actingAs($user)->get('/supervisor');
        $response1->assertStatus(403);

        $response2 = $this->actingAs($user)->get('/admin');
        $response2->assertStatus(403);
    }
}
