<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'original_name' => 'screenshot.png',
            'file_path' => 'attachments/'.Str::uuid().'.png',
            'mime_type' => 'image/png',
            'file_size' => fake()->numberBetween(1024, 2 * 1024 * 1024),
        ];
    }
}
