<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIrrigationAreaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable'],
            'year_built' => ['nullable'],
            'authority' => ['required'],
            'land_area' => ['nullable'],
            'code' => ['required'],
            'river_region' => ['nullable'],
            'watershed' => ['nullable'],
            'das' => ['nullable'],
            'created_ip' => ['nullable'],
            'updated_ip' => ['nullable'],
            'deleted_by' => ['nullable'],
            'deleted_ip' => ['nullable'],
            'category' => ['nullable'],
            'name' => ['required'],
            'value' => ['nullable'],
            'unit' => ['nullable'],
            'water_source' => ['nullable'],
            'irrigation_types' => ['nullable'],
            'condition' => ['nullable'],
            'iksi_value' => ['nullable'],
        ];
    }
}
