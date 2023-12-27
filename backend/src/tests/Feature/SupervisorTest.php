<?php

namespace Tests\Feature;

use Database\Factories\Authorized_usersFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SupervisorTest extends TestCase
{
    use DatabaseTransactions;

    public function test_granting_login_privileges_to_user_should_succeed()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $nonAuthorizedUser = UserFactory::new()->withoutPrivileges()->create();


        $response = $this->actingAs($user)
            ->json('POST', '/grant-privileges', [
                'email' => $nonAuthorizedUser->email,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => 'Successfully granted login privileges.'
        ]);

        $this->assertDatabaseHas('authorized_users', [
            'email' => $nonAuthorizedUser->email,
        ]);
    }

    public function test_granting_login_privileges_to_already_authorized_user_should_fail() {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user2 = Authorized_usersFactory::new()->create([
            'email' => $user2->email,
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/grant-privileges', [
                'email' => $user2->email,
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Failed to grant login privileges. Reason: User already has login privileges.'
        ]);
    }

    public function test_non_authorized_user_tries_to_grant_login_privileges_should_fail() {
        $user = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withoutPrivileges()->create();

        $response = $this->actingAs($user)
            ->json('POST', '/grant-privileges', [
                'email' => $user2->email,
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Unauthorized action.'
        ]);
    }

    public function test_revoking_login_privileges_to_existing_user_should_succeed() {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user2 = Authorized_usersFactory::new()->create([
            'email' => $user2->email,
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/revoke-privileges', [
                'email' => $user2->email,
            ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => 'Successfully revoked login privileges.'
        ]);
    }

    public function test_revoking_login_privileges_to_non_existing_user_should_fail() {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withoutPrivileges()->create();

        $response = $this->actingAs($user)
            ->json('POST', '/revoke-privileges', [
                'email' => $user2->email,
            ]);
        
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Failed to revoke login privileges. Reason: User does not have login privileges.'
        ]);
    }

    public function test_revoking_login_privileges_supervisor_or_admin_should_fail() {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withPrivileges()->create();
        $authorized_user2 = Authorized_usersFactory::new()->create([
            'email' => $user2->email,
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/revoke-privileges', [
                'email' => $user2->email,
            ]);
        
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Failed to revoke login privileges. Reason: User is a supervisor or admin. Cannot revoke login privileges.'
        ]);
    }

    public function test_non_authorized_user_tries_to_revoke_login_privileges_should_fail() {
        $user = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withoutPrivileges()->create();

        $response = $this->actingAs($user)
            ->json('POST', '/revoke-privileges', [
                'email' => $user2->email,
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Unauthorized action.'
        ]);
    }
}