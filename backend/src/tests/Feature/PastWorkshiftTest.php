<?php

namespace Tests\Feature;

use Database\Factories\Authorized_usersFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PastWorkshiftTest extends TestCase
{
    use DatabaseTransactions;

    public function test_send_empty_values_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/get-past-work-shift', [
                'start_date' => '',
                'end_date' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'start_date' => ['The start date field is required.'],
                'end_date' => ['The end date field is required.'],
            ]
        ]);
    }

    public function test_send_only_start_date_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/get-past-work-shift', [
                'start_date' => '2021-01-01',
                'end_date' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'end_date' => ['The end date field is required.'],
            ]
        ]);
    }

    public function test_send_only_end_date_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/get-past-work-shift', [
                'start_date' => '',
                'end_date' => '2021-01-01',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'start_date' => ['The start date field is required.'],
            ]
        ]);
    }

    public function test_send_incomplete_data_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/get-past-work-shift', [
                'start_date' => '2021-01',
                'end_date' => '2021-01-02',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'start_date' => ['The start date does not match the format Y-m-d.'],
            ]
        ]);
    }

    public function test_end_date_is_before_start_date_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/get-past-work-shift', [
                'start_date' => '2021-01-02',
                'end_date' => '2021-01-01',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'end_date' => ['end_date must be later than or equal to start date.'],
            ]
        ]);
    }
}
