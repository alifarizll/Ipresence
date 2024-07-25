<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IrrigationAreaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'created_at' => $this->created_at,
            'category_id' => $this->category_id,
            'year_built' => $this->year_built,
            'updated_at' => $this->updated_at,
            'authority' => $this->authority,
            'land_area' => $this->land_area,
            'deleted_at' => $this->deleted_at,
            'code' => $this->code,
            'id' => $this->id,
            'river_region' => $this->river_region,
            'watershed' => $this->watershed,
            'das' => $this->das,
            'created_by' => $this->created_by,
            'created_ip' => $this->created_ip,
            'updated_by' => $this->updated_by,
            'updated_ip' => $this->updated_ip,
            'deleted_by' => $this->deleted_by,
            'deleted_ip' => $this->deleted_ip,
            'category' => $this->category,
            'name' => $this->name,
            'value' => $this->value,
            'unit' => $this->unit,
            'water_source' => $this->water_source,
            'irrigation_types' => $this->irrigation_types,
            'condition' => $this->condition,
            'iksi_value' => $this->iksi_value,
        ];
    }
}
