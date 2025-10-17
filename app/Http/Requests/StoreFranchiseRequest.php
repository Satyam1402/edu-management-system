<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFranchiseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('super_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:20', 'unique:franchises,code'],
            'email' => ['nullable', 'email', 'unique:franchises,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive']
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Franchise name is required.',
            'code.unique' => 'This franchise code is already taken.',
            'email.unique' => 'This email is already registered.'
        ];
    }
}
