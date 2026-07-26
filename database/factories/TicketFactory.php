<?php

namespace Database\Factories;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Application;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sequence = fake()->unique()->numberBetween(1, 9999);

        return [
            'ticket_number' => 'TCK-'
                .now()->format('Ym')
                .'-'
                .str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'reporter_id' => User::factory(),
            'application_id' => Application::factory(),
            'assigned_to' => null,
            'title' => fake()->sentence(6),
            'category' => fake()->randomElement(TicketCategory::cases()),
            'priority' => TicketPriority::Medium,
            'status' => TicketStatus::New,
            'description' => fake()->paragraph(),
            'reproduction_steps' => fake()->paragraph(),
            'analysis_notes' => null,
            'resolution_notes' => null,
            'resolved_at' => null,
        ];
    }
}
