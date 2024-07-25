<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePositionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'agency_unit_id' => ['required'],
            'agency_id' => ['required'],
            'updated_ip' => ['nullable'],
            'deleted_by' => ['nullable'],
            'deleted_ip' => ['nullable'],
            'name' => ['required'],
            'acronym' => ['nullable'],
            'created_ip' => ['nullable'],
        ];
    }
}
