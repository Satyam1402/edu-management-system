<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['super_admin', 'franchise']);
    }

    public function rules(): array
    {
        return [
            'franchise_id' => ['required', 'exists:franchises,id'],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'unique:students,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:enquiry,admission,active,inactive']
        ];
    }
}
