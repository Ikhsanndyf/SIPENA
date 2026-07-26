<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttachmentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_attachment_belongs_to_ticket(): void
    {
        $ticket = Ticket::factory()->create();
        $attachment = Attachment::factory()->for($ticket)->create();

        $this->assertTrue($attachment->ticket->is($ticket));
        $this->assertTrue($ticket->attachment->is($attachment));
    }

    public function test_ticket_cannot_have_more_than_one_attachment(): void
    {
        $ticket = Ticket::factory()->create();

        Attachment::factory()->for($ticket)->create();

        $this->expectException(QueryException::class);

        Attachment::factory()->for($ticket)->create();
    }

    public function test_attachment_is_deleted_with_ticket(): void
    {
        $ticket = Ticket::factory()->create();
        $attachment = Attachment::factory()->for($ticket)->create();

        $ticket->delete();

        $this->assertDatabaseMissing('attachments', [
            'id' => $attachment->id,
        ]);
    }
}
