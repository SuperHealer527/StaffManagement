<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['name' => fake()->company(), 'type' => 'corporation', 'phone' => fake()->phoneNumber(), 'proposal_status' => 'uncontacted', 'sales_owner_id' => User::factory()->state(['role' => 'employee', 'account_status' => 'active'])];
    }
}
