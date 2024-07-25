<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'province_id' => $this->province_id,
            'regency_id' => $this->regency_id,
            'land_area' => $this->land_area,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'updated_ip' => $this->updated_ip,
            'deleted_ip' => $this->deleted_ip,
            'created_by' => $this->created_by,
            'created_ip' => $this->created_ip,
            'deleted_by' => $this->deleted_by,
            'updated_by' => $this->updated_by,
            'code_bps' => $this->code_bps,
            'code_dagri' => $this->code_dagri,
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}
