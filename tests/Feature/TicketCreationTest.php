<?php

namespace Tests\Feature;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Application;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketCreationTest extends TestCase
{
    use RefreshDatabase;

    // Reporter membuat tiket valid dengan nilai awal dari sistem.
    public function test_reporter_can_create_valid_ticket(): void
    {
        $reporter = User::factory()->create();
        $application = Application::factory()->create();

        $response = $this
            ->actingAs($reporter)
            ->post(route('tickets.store'), $this->validPayload($application));

        $response
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $ticket = Ticket::query()->sole();
        $expectedNumber = sprintf(
            'TCK-%s-%04d',
            $ticket->created_at->format('Ym'),
            $ticket->id,
        );

        $this->assertSame($reporter->id, $ticket->reporter_id);
        $this->assertSame($expectedNumber, $ticket->ticket_number);
        $this->assertSame(TicketStatus::New, $ticket->status);
        $this->assertSame(TicketPriority::Medium, $ticket->priority);
        $this->assertNull($ticket->assigned_to);
    }

    // Reporter membuat tiket dengan satu lampiran gambar.
    public function test_reporter_can_create_ticket_with_attachment(): void
    {
        Storage::fake('public');

        $reporter = User::factory()->create();
        $application = Application::factory()->create();
        $payload = $this->validPayload($application);
        $payload['attachment'] = UploadedFile::fake()->image('screenshot.png');

        $this
            ->actingAs($reporter)
            ->post(route('tickets.store'), $payload)
            ->assertRedirect(route('dashboard'));

        $ticket = Ticket::query()->sole();
        $attachment = $ticket->attachment()->firstOrFail();

        $this->assertSame('screenshot.png', $attachment->original_name);
        $this->assertSame('image/png', $attachment->mime_type);
        Storage::disk('public')->assertExists($attachment->file_path);
    }

    // Field wajib harus ditolak ketika tidak diisi.
    public function test_required_ticket_fields_are_validated(): void
    {
        $reporter = User::factory()->create();

        $this
            ->actingAs($reporter)
            ->post(route('tickets.store'))
            ->assertSessionHasErrors([
                'application_id',
                'title',
                'category',
                'description',
            ]);

        $this->assertDatabaseCount('tickets', 0);
    }

    // Deskripsi tiket harus memenuhi panjang minimal.
    public function test_short_ticket_description_is_rejected(): void
    {
        $reporter = User::factory()->create();
        $application = Application::factory()->create();
        $payload = $this->validPayload($application);
        $payload['description'] = 'Terlalu pendek';

        $this
            ->actingAs($reporter)
            ->post(route('tickets.store'), $payload)
            ->assertSessionHasErrors('description');

        $this->assertDatabaseCount('tickets', 0);
    }

    // Developer tidak dapat membuat tiket sebagai reporter.
    public function test_developer_cannot_create_ticket(): void
    {
        $developer = User::factory()->developer()->create();
        $application = Application::factory()->create();

        $this
            ->actingAs($developer)
            ->post(route('tickets.store'), $this->validPayload($application))
            ->assertForbidden();

        $this->assertDatabaseCount('tickets', 0);
    }

    // Field penanganan yang dimanipulasi tidak ikut disimpan.
    public function test_reporter_cannot_manipulate_system_and_handling_fields(): void
    {
        $reporter = User::factory()->create();
        $otherReporter = User::factory()->create();
        $developer = User::factory()->developer()->create();
        $application = Application::factory()->create();

        $payload = [
            ...$this->validPayload($application),
            'ticket_number' => 'TCK-MANIPULATED',
            'reporter_id' => $otherReporter->id,
            'assigned_to' => $developer->id,
            'priority' => TicketPriority::Critical->value,
            'status' => TicketStatus::Resolved->value,
            'analysis_notes' => 'Manipulasi analisis.',
            'resolution_notes' => 'Manipulasi solusi.',
        ];

        $this
            ->actingAs($reporter)
            ->post(route('tickets.store'), $payload)
            ->assertRedirect(route('dashboard'));

        $ticket = Ticket::query()->sole();

        $this->assertSame($reporter->id, $ticket->reporter_id);
        $this->assertNotSame('TCK-MANIPULATED', $ticket->ticket_number);
        $this->assertNull($ticket->assigned_to);
        $this->assertSame(TicketPriority::Medium, $ticket->priority);
        $this->assertSame(TicketStatus::New, $ticket->status);
        $this->assertNull($ticket->analysis_notes);
        $this->assertNull($ticket->resolution_notes);
    }

    /**
     * Data tiket valid yang dapat digunakan ulang oleh test.
     *
     * @return array<string, mixed>
     */
    private function validPayload(Application $application): array
    {
        return [
            'application_id' => $application->id,
            'title' => 'Data transaksi gagal tersimpan',
            'category' => TicketCategory::Data->value,
            'description' => 'Data transaksi tidak tersimpan setelah tombol simpan ditekan.',
            'reproduction_steps' => 'Buka form, isi data, kemudian tekan tombol simpan.',
        ];
    }
}
