<?php

namespace App\Http\Requests;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class TicketFilterRequest extends FormRequest
{
    /**
     * Filter hanya digunakan oleh pengguna yang telah login.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Memvalidasi seluruh parameter pencarian dan filter tiket.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Kata pencarian dibatasi agar query tetap wajar.
            'search' => ['nullable', 'string', 'max:100'],

            // Filter master dan enum harus menunjuk nilai yang tersedia.
            'application_id' => [
                'nullable',
                'integer',
                Rule::exists('applications', 'id'),
            ],
            'status' => ['nullable', new Enum(TicketStatus::class)],
            'priority' => ['nullable', new Enum(TicketPriority::class)],
            'category' => ['nullable', new Enum(TicketCategory::class)],

            // PIC menerima ID developer atau nilai khusus unassigned.
            'assigned_to' => [
                'nullable',
                Rule::when(
                    $this->input('assigned_to') === 'unassigned',
                    Rule::in(['unassigned']),
                    Rule::exists('users', 'id')
                        ->where('role', UserRole::Developer->value),
                ),
            ],

            // Rentang tanggal menggunakan format HTML date.
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => [
                'nullable',
                'date_format:Y-m-d',
                Rule::when(
                    $this->filled('date_from'),
                    'after_or_equal:date_from',
                ),
            ],
        ];
    }
}
