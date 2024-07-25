<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLevelRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'created_ip' => ['nullable'],
            'deleted_by' => ['nullable'],
            'updated_ip' => ['nullable'],
            'name' => ['required'],
            'description' => ['nullable'],
            'deleted_ip' => ['nullable'],
        ];
    }
}
