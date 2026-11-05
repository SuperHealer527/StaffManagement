<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['client_id' => Client::factory(), 'business_name' => '地域イベント運営', 'name' => fake()->word().'イベント', 'event_date' => now()->addWeek(), 'meeting_time' => '08:30', 'start_time' => '09:00', 'end_time' => '17:00', 'dismissal_time' => '17:30', 'required_staff' => 5, 'status' => 'recruiting', 'created_by' => User::factory()->state(['role' => 'employee', 'account_status' => 'active'])];
    }
}
