<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'district_id' => $this->district_id,
            'province_id' => $this->province_id,
            'updated_at' => $this->updated_at,
            'land_area' => $this->land_area,
            'regency_id' => $this->regency_id,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'id' => $this->id,
            'updated_by' => $this->updated_by,
            'updated_ip' => $this->updated_ip,
            'deleted_by' => $this->deleted_by,
            'deleted_ip' => $this->deleted_ip,
            'code_bps' => $this->code_bps,
            'code_dagri' => $this->code_dagri,
            'code' => $this->code,
            'name' => $this->name,
            'created_by' => $this->created_by,
            'created_ip' => $this->created_ip,
        ];
    }
}
