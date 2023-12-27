<?php

namespace Tests\Feature;

use Database\Factories\Authorized_usersFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateNewsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_send_empty_values_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/editorjsJsonUpload', [
                'title' => '',
                'content' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'title' => ['The title field is required.'],
            ]
        ]);
    }

    public function test_non_authorized_user_send_data_should_fail() {
        $user = UserFactory::new()->withoutPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/editorjsJsonUpload', [
                'title' => 'Test title',
                'content' => 'Test content',
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Unauthorized action.'
        ]);
    }
}
