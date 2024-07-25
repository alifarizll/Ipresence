<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeriodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'updated_at' => $this->updated_at,
			'created_at' => $this->created_at,
			'start_date' => $this->start_date,
			'deleted_at' => $this->deleted_at,
			'end_date' => $this->end_date,
			'id' => $this->id,
			'updated_ip' => $this->updated_ip,
			'deleted_by' => $this->deleted_by,
			'deleted_ip' => $this->deleted_ip,
			'name' => $this->name,
			'label' => $this->label,
			'created_by' => $this->created_by,
			'created_ip' => $this->created_ip,
			'updated_by' => $this->updated_by,
		];
    }
}
