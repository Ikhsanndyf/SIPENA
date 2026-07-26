<?php

namespace App\Http\Requests;

use App\Enums\TicketCategory;
use App\Models\Ticket;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Memastikan hanya reporter pemilik tiket new yang dapat mengubahnya.
     */
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        return $ticket instanceof Ticket
            && ($this->user()?->can('update', $ticket) ?? false);
    }

    /**
     * Menentukan field reporter yang dapat diperbarui.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Informasi utama kendala.
            'application_id' => ['required', 'exists:applications,id'],
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'category' => ['required', new Enum(TicketCategory::class)],

            // Detail terjadinya kendala.
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'reproduction_steps' => ['nullable', 'string', 'max:5000'],

            // Lampiran baru bersifat opsional dan menggantikan lampiran lama.
            'attachment' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }
}
