<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'created_ip' => $this->created_ip,
            'deleted_by' => $this->deleted_by,
            'updated_by' => $this->updated_by,
            'updated_ip' => $this->updated_ip,
            'name' => $this->name,
            'description' => $this->description,
            'deleted_ip' => $this->deleted_ip,
        ];
    }
}
