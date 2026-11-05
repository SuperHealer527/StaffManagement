<?php

namespace Database\Factories;

use App\Models\InboundEmail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InboundEmail>
 */
class InboundEmailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_message_id' => fake()->uuid(),
            'sender_email' => fake()->safeEmail(),
            'recipient_emails' => [fake()->safeEmail()],
            'subject' => fake()->sentence(),
            'body_excerpt' => fake()->paragraph(),
            'status' => 'pending',
            'received_at' => now(),
        ];
    }
}
