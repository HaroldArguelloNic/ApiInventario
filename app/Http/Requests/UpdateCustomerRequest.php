<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name'=>['sometimes','string'],
            'last_name'=>['sometimes','string'],
            'email'=>['sometimes','string','email'],
            'phone'=>['sometimes','string','max:30'],
            'address'=>['sometimes','string','max:100'],
        ];
    }
}
