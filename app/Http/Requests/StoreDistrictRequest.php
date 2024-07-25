<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistrictRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'province_id' => ['required'],
            'regency_id' => ['required'],
            'land_area' => ['required'],
            'updated_ip' => ['nullable'],
            'deleted_ip' => ['nullable'],
            'created_ip' => ['nullable'],
            'deleted_by' => ['nullable'],
            'code_bps' => ['nullable'],
            'code_dagri' => ['nullable'],
            'code' => ['required'],
            'name' => ['required'],
        ];
    }
}
