<?php

namespace Database\Factories;

use App\Models\Interview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interview>
 */
class InterviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_user_id' => User::factory()->state(['role' => 'staff', 'account_status' => 'active']),
            'manager_id' => User::factory()->state(['role' => 'employee', 'account_status' => 'active']),
            'type' => 'regular',
            'method' => 'in_person',
            'location' => '事務所',
            'candidate_times' => [now()->addDays(2)->toDateTimeString()],
        ];
    }
}
