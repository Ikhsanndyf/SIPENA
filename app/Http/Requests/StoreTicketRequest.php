<?php

namespace App\Http\Requests;

use App\Enums\TicketCategory;
use App\Models\Ticket;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Ticket::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'application_id' => ['required', 'exists:applications,id'],
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'category' => ['required', new Enum(TicketCategory::class)],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'reproduction_steps' => ['nullable', 'string', 'max:5000'],
            'attachment' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }
}
