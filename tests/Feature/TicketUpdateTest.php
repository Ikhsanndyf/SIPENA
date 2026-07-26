<?php

namespace Tests\Feature;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Application;
use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketUpdateTest extends TestCase
{
    use RefreshDatabase;

    // Guest harus login sebelum membuka formulir edit.
    public function test_guest_is_redirected_to_login_from_ticket_edit(): void
    {
        $ticket = Ticket::factory()->create();

        $this
            ->get(route('tickets.edit', $ticket))
            ->assertRedirect(route('login'));
    }

    // Reporter pemilik dapat membuka formulir tiket berstatus new.
    public function test_owner_can_view_edit_form_for_new_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::New]);

        $this
            ->actingAs($reporter)
            ->get(route('tickets.edit', $ticket))
            ->assertOk()
            ->assertViewIs('tickets.edit')
            ->assertViewHas('ticket', fn (Ticket $viewTicket): bool => $viewTicket->is($ticket))
            ->assertSee($ticket->ticket_number)
            ->assertSee($ticket->title);
    }

    // Reporter lain tidak dapat membuka atau memperbarui tiket.
    public function test_other_reporter_cannot_edit_or_update_ticket(): void
    {
        $owner = User::factory()->create();
        $otherReporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($owner, 'reporter')
            ->create(['status' => TicketStatus::New]);
        $application = Application::factory()->create();

        $this
            ->actingAs($otherReporter)
            ->get(route('tickets.edit', $ticket))
            ->assertForbidden();

        $this
            ->actingAs($otherReporter)
            ->put(route('tickets.update', $ticket), $this->validPayload($application))
            ->assertForbidden();
    }

    // Tiket yang sudah diproses tidak dapat diubah reporter.
    public function test_owner_cannot_edit_or_update_processed_ticket(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::Analyzed]);
        $application = Application::factory()->create();

        $this
            ->actingAs($reporter)
            ->get(route('tickets.edit', $ticket))
            ->assertForbidden();

        $this
            ->actingAs($reporter)
            ->put(route('tickets.update', $ticket), $this->validPayload($application))
            ->assertForbidden();
    }

    // Developer tidak dapat mengubah field laporan milik reporter.
    public function test_developer_cannot_edit_or_update_reporter_ticket(): void
    {
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()->create(['status' => TicketStatus::New]);
        $application = Application::factory()->create();

        $this
            ->actingAs($developer)
            ->get(route('tickets.edit', $ticket))
            ->assertForbidden();

        $this
            ->actingAs($developer)
            ->put(route('tickets.update', $ticket), $this->validPayload($application))
            ->assertForbidden();
    }

    // Reporter memperbarui field kendala tanpa mengubah field sistem.
    public function test_owner_can_update_new_ticket_without_manipulating_system_fields(): void
    {
        $reporter = User::factory()->create();
        $otherReporter = User::factory()->create();
        $developer = User::factory()->developer()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::New]);
        $originalTicketNumber = $ticket->ticket_number;
        $application = Application::factory()->create();

        $payload = [
            ...$this->validPayload($application),
            'ticket_number' => 'TCK-MANIPULATED',
            'reporter_id' => $otherReporter->id,
            'assigned_to' => $developer->id,
            'priority' => TicketPriority::Critical->value,
            'status' => TicketStatus::Resolved->value,
            'analysis_notes' => 'Analisis yang dimanipulasi.',
            'resolution_notes' => 'Solusi yang dimanipulasi.',
        ];

        $this
            ->actingAs($reporter)
            ->put(route('tickets.update', $ticket), $payload)
            ->assertRedirect(route('tickets.show', $ticket))
            ->assertSessionHas('success');

        $ticket->refresh();

        $this->assertSame($application->id, $ticket->application_id);
        $this->assertSame($payload['title'], $ticket->title);
        $this->assertSame(TicketCategory::Access, $ticket->category);
        $this->assertSame($originalTicketNumber, $ticket->ticket_number);
        $this->assertSame($reporter->id, $ticket->reporter_id);
        $this->assertNull($ticket->assigned_to);
        $this->assertSame(TicketPriority::Medium, $ticket->priority);
        $this->assertSame(TicketStatus::New, $ticket->status);
        $this->assertNull($ticket->analysis_notes);
        $this->assertNull($ticket->resolution_notes);
    }

    // Field wajib dan batas panjang tetap divalidasi saat update.
    public function test_ticket_update_fields_are_validated(): void
    {
        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::New]);
        $originalTitle = $ticket->title;

        $this
            ->actingAs($reporter)
            ->put(route('tickets.update', $ticket), [
                'title' => 'Abc',
                'description' => 'Pendek',
            ])
            ->assertSessionHasErrors([
                'application_id',
                'title',
                'category',
                'description',
            ]);

        $this->assertSame($originalTitle, $ticket->fresh()->title);
    }

    // Lampiran lama dipertahankan jika tidak ada unggahan baru.
    public function test_existing_attachment_is_preserved_without_replacement(): void
    {
        Storage::fake('public');

        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::New]);
        $attachment = Attachment::factory()
            ->for($ticket)
            ->create(['file_path' => 'attachments/current.png']);
        Storage::disk('public')->put($attachment->file_path, 'current-file');
        $application = Application::factory()->create();

        $this
            ->actingAs($reporter)
            ->put(route('tickets.update', $ticket), $this->validPayload($application))
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertSame($attachment->file_path, $attachment->fresh()->file_path);
        Storage::disk('public')->assertExists($attachment->file_path);
    }

    // Lampiran baru mengganti metadata dan menghapus file lama.
    public function test_owner_can_replace_ticket_attachment(): void
    {
        Storage::fake('public');

        $reporter = User::factory()->create();
        $ticket = Ticket::factory()
            ->for($reporter, 'reporter')
            ->create(['status' => TicketStatus::New]);
        $attachment = Attachment::factory()
            ->for($ticket)
            ->create(['file_path' => 'attachments/old.png']);
        Storage::disk('public')->put($attachment->file_path, 'old-file');
        $oldPath = $attachment->file_path;
        $application = Application::factory()->create();
        $payload = [
            ...$this->validPayload($application),
            'attachment' => UploadedFile::fake()->image('new-screenshot.png'),
        ];

        $this
            ->actingAs($reporter)
            ->put(route('tickets.update', $ticket), $payload)
            ->assertRedirect(route('tickets.show', $ticket));

        $attachment->refresh();

        $this->assertSame('new-screenshot.png', $attachment->original_name);
        $this->assertNotSame($oldPath, $attachment->file_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($attachment->file_path);
    }

    /**
     * Menyediakan data update tiket yang valid untuk digunakan ulang.
     *
     * @return array<string, mixed>
     */
    private function validPayload(Application $application): array
    {
        return [
            'application_id' => $application->id,
            'title' => 'Akses pengguna perlu diperbarui',
            'category' => TicketCategory::Access->value,
            'description' => 'Pengguna tidak dapat membuka menu yang sebelumnya dapat diakses.',
            'reproduction_steps' => 'Masuk sebagai pengguna, kemudian buka menu pengelolaan data.',
        ];
    }
}
