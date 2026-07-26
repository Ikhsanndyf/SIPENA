<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketStatusHistory>
 */
class TicketStatusHistoryFactory extends Factory
{
    /**
     * Menyediakan data riwayat status untuk pengujian.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'changed_by' => User::factory(),
            'from_status' => TicketStatus::New,
            'to_status' => TicketStatus::Analyzed,
            'notes' => fake()->sentence(),
        ];
    }
}
