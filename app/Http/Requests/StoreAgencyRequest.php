<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgencyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'province_id' => ['nullable'],
            'regency_id' => ['nullable'],
            'level_id' => ['required'],
            'deleted_by' => ['nullable'],
            'deleted_ip' => ['nullable'],
            'name' => ['required'],
            'acronym' => ['required'],
            'created_ip' => ['nullable'],
            'updated_ip' => ['nullable'],
        ];
    }
}
