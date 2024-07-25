<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProvinceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'land_area' => $this->land_area,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
            'updated_at' => $this->updated_at,
            'id' => $this->id,
            'created_by' => $this->created_by,
            'created_ip' => $this->created_ip,
            'updated_by' => $this->updated_by,
            'updated_ip' => $this->updated_ip,
            'deleted_by' => $this->deleted_by,
            'deleted_ip' => $this->deleted_ip,
            'code_bps' => $this->code_bps,
            'code_dagri' => $this->code_dagri,
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}
