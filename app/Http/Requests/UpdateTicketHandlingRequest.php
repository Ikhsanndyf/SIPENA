<?php

namespace App\Http\Requests;

use App\Enums\TicketPriority;
use App\Enums\UserRole;
use App\Models\Ticket;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateTicketHandlingRequest extends FormRequest
{
    /**
     * Memastikan hanya developer yang dapat menyimpan penanganan tiket.
     */
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        return $ticket instanceof Ticket
            && ($this->user()?->can('handle', $ticket) ?? false);
    }

    /**
     * Menentukan field penanganan yang dapat disimpan developer.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // PIC harus kosong atau menunjuk pengguna dengan role developer.
            'assigned_to' => [
                'nullable',
                Rule::exists('users', 'id')
                    ->where('role', UserRole::Developer->value),
            ],

            // Prioritas harus mengikuti enum domain tiket.
            'priority' => ['required', new Enum(TicketPriority::class)],

            // Catatan penanganan dibatasi agar tetap terkontrol.
            'analysis_notes' => ['nullable', 'string', 'max:5000'],
            'resolution_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
