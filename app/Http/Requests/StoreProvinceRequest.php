<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProvinceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'land_area' => ['required'],
            'created_ip' => ['nullable'],
            'updated_ip' => ['nullable'],
            'deleted_by' => ['nullable'],
            'deleted_ip' => ['nullable'],
            'code_bps' => ['nullable'],
            'code_dagri' => ['nullable'],
            'code' => ['required'],
            'name' => ['required'],
        ];
    }
}
