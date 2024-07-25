<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlantingPhaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_number' => ['required'],
            'updated_ip' => ['nullable'],
            'deleted_by' => ['nullable'],
            'deleted_ip' => ['nullable'],
            'name' => ['required'],
            'description' => ['nullable'],
            'created_ip' => ['nullable'],
        ];
    }
}
