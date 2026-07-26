<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketCommentRequest extends FormRequest
{
    /**
     * Memastikan pengguna memiliki akses terhadap tiket.
     */
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        return $ticket instanceof Ticket
            && ($this->user()?->can(
                'create',
                [TicketComment::class, $ticket],
            ) ?? false);
    }

    /**
     * Memvalidasi isi komentar sesuai batas PRD.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:2', 'max:2000'],
        ];
    }
}
