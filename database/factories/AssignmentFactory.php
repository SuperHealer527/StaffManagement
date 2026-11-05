<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['event_id' => Event::factory(), 'user_id' => User::factory()->state(['role' => 'staff', 'account_status' => 'active']), 'status' => 'confirmed', 'fee' => 9000, 'transportation' => 480, 'payment_status' => 'unconfirmed'];
    }
}
