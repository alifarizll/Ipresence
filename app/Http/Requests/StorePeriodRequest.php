<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeriodRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
			'start_date' => ['required'],
			'end_date' => ['required'],
			'updated_ip' => ['nullable'],
			'deleted_by' => ['nullable'],
			'deleted_ip' => ['nullable'],
			'name' => ['required'],
			'label' => ['required'],
			'created_ip' => ['nullable'],
		];
    }
}

