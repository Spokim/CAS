<?php

namespace Tests\Feature;

use Database\Factories\Authorized_usersFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WorkshiftTest extends TestCase
{
    use DatabaseTransactions;

    public function test_send_empty_values_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)->post('/post-work-data', [
            'start_time' => '',
            'end_time' => '',
            'date' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'start_time' => 'The start time field is required.',
            'end_time' => 'The end time field is required.',
            'date' => 'The date field is required.',
        ]);
    }

    public function test_send_incomplete_data_should_fail()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        // Incorrect End Time (HH:M)
        $response = $this->actingAs($user)->post('/post-work-data', [
            'start_time' => '10:00',
            'end_time' => '12:2',
            'date' => '2023-11-05',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'end_time' => 'The end time format is invalid.',
        ]);

        // Incorrect Date (YYYY-MM-D)
        $response2 = $this->actingAs($user)->post('/post-work-data', [
            'start_time' => '10:00',
            'end_time' => '12:00',
            'date' => '2023-11-0',
        ]);

        $response2->assertStatus(302);
        $response2->assertSessionHasErrors([
            'date' => 'The date format is invalid.',
        ]);

        // Incorrect Start Time (HH:MMM)
        $response3 = $this->actingAs($user)->post('/post-work-data', [
            'start_time' => '10:000',
            'end_time' => '12:00',
            'date' => '2023-11-05',
        ]);
        $response3->assertStatus(302);
        $response3->assertSessionHasErrors([
            'start_time' => 'The start time format is invalid.',
        ]);

        // Incorrect Time Above 24H, above 60M (HH:MM)
        $response4 = $this->actingAs($user)->post('/post-work-data', [
            'start_time' => '25:00',
            'end_time' => '12:61',
            'date' => '2023-11-05',
        ]);
        $response4->assertStatus(302);
        $response4->assertSessionHasErrors([
            'start_time' => 'The start time format is invalid.',
            'end_time' => 'The end time format is invalid.',
        ]);
    }

    public function test_end_time_is_not_before_start_time()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user)->post('/post-work-data', [
            'start_time' => '12:00',
            'end_time' => '10:00',
            'date' => '2023-11-05',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'end_time' => 'The end time must be after the start time.',
        ]);
    }

    public function test_user_already_has_a_work_shift_during_that_time_range()
    {
        $user = UserFactory::new()->withPrivileges()->create();
        $authorized_user = Authorized_usersFactory::new()->create([
            'email' => $user->email,
        ]);

        $initialShift = $this->actingAs($user)->post('/post-work-data', [
            'start_time' => '10:00',
            'end_time' => '15:00',
            'date' => '2023-11-05',
        ]);
        $initialShift->assertStatus(302);
        $initialShift->assertSessionHasNoErrors();
    
        // Starts before, ends during
        $mondaiShift1 = $this->actingAs($user)->post('post-work-data', [
            'start_time' => '09:00',
            'end_time' => '14:00',
            'date' => '2023-11-05',
        ]);
        $mondaiShift1->assertSessionHas('error', 'Failed to create work shift. Reason:' . "\n" . 'A work shift already exists during the specified time range.');
        $mondaiShift1->assertRedirect(route('work-shift'));

        // Starts before, ends after
        $mondaiShift2 = $this->actingAs($user)->post('post-work-data', [
            'start_time' => '09:00',
            'end_time' => '16:00',
            'date' => '2023-11-05',
        ]);
        $mondaiShift2->assertSessionHas('error', 'Failed to create work shift. Reason:' . "\n" . 'A work shift already exists during the specified time range.');
        $mondaiShift2->assertRedirect(route('work-shift'));

        // Starts during, ends during
        $mondaiShift3 = $this->actingAs($user)->post('post-work-data', [
            'start_time' => '11:00',
            'end_time' => '14:00',
            'date' => '2023-11-05',
        ]);
        $mondaiShift3->assertSessionHas('error', 'Failed to create work shift. Reason:' . "\n" . 'A work shift already exists during the specified time range.');
        $mondaiShift3->assertRedirect(route('work-shift'));

        // Starts during, ends after
        $mondaiShift4 = $this->actingAs($user)->post('post-work-data', [
            'start_time' => '11:00',
            'end_time' => '16:00',
            'date' => '2023-11-05',
        ]);
        $mondaiShift4->assertSessionHas('error', 'Failed to create work shift. Reason:' . "\n" . 'A work shift already exists during the specified time range.');
        $mondaiShift4->assertRedirect(route('work-shift'));
    }
}
