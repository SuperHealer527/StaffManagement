<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meeting>
 */
class MeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'recorded_by' => User::factory(),
            'type' => fake()->randomElement(['sales', 'preparation', 'consultation', 'follow_up', 'other']),
            'meeting_date' => fake()->dateTimeBetween('-1 year', '+1 month'),
            'method' => fake()->randomElement(['visit', 'phone', 'online', 'email', 'other']),
            'participants' => fake()->name(),
            'content' => fake()->paragraph(),
        ];
    }
}
