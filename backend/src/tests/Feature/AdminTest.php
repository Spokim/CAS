<?php

namespace Tests\Feature;

use Database\Factories\Authorized_usersFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    public function test_granting_Supervisor_privileges_to_user_should_succeed()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user2 = Authorized_usersFactory::new()->create([
            'email' => $user2->email,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'register_privileges' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('grant-supervisor-privileges'), [
            'email' => $user2->email,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'register_privileges' => 1,
        ]);
    }

    public function test_granting_Supervisor_privileges_to_non_existing_user_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'test@test.com',
        ]);

        $response = $this->actingAs($user)->post(route('grant-supervisor-privileges'), [
            'email' => 'test@test.com',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Failed to grant supervisor privileges. Reason: User does not exist.'
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'test@test.com',
            'register_privileges' => 1,
        ]);
    }

    public function test_granting_Supervisor_privileges_to_already_privileged_user_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withPrivileges()->create();
        $authorized_user2 = Authorized_usersFactory::new()->create([
            'email' => $user2->email,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'register_privileges' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('grant-supervisor-privileges'), [
            'email' => $user2->email,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Failed to grant supervisor privileges. Reason: User already has supervisor privileges.'
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'register_privileges' => 1,
        ]);
    }

    public function test_non_authorized_user_tries_to_grant_supervisor_privileges_should_fail()
    {
        $user = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withoutPrivileges()->create();

        $response = $this->actingAs($user)
            ->json('POST', '/grant-supervisor-privileges', [
                'email' => $user2->email,
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Unauthorized action.'
        ]);
    }

    public function test_revoking_Supervisor_privileges_to_user_should_succeed()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user2 = Authorized_usersFactory::new()->create([
            'email' => $user2->email,
        ]);
        $this->actingAs($user)->post(route('grant-supervisor-privileges'), [
            'email' => $user2->email,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'register_privileges' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('revoke-supervisor-privileges'), [
            'email' => $user2->email,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'register_privileges' => 0,
        ]);
    }

    public function test_revoking_Supervisor_privileges_to_non_existing_user_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'test@test.com',
        ]);

        $response = $this->actingAs($user)->post(route('revoke-supervisor-privileges'), [
            'email' => 'test@test.com',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Failed to revoke supervisor privileges. Reason: User does not exist.'
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'test@test.com'
        ]);
    }

    public function test_revoking_Supervisor_privileges_to_non_privileged_user_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user2 = Authorized_usersFactory::new()->create([
            'email' => $user2->email,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'register_privileges' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('revoke-supervisor-privileges'), [
            'email' => $user2->email,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Failed to revoke supervisor privileges. Reason: User does not have supervisor privileges.'
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'register_privileges' => 0,
        ]);
    }

    public function test_non_authorized_user_tries_to_revoke_supervisor_privileges_should_fail()
    {
        $user = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $user2 = UserFactory::new()->withoutPrivileges()->create();

        $response = $this->actingAs($user)
            ->json('POST', '/revoke-supervisor-privileges', [
                'email' => $user2->email,
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Unauthorized action.'
        ]);
    }
}
