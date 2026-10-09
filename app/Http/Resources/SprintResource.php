<?php

namespace App\Http\Resources;

use App\Models\Sprint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Sprint
 */
class SprintResource extends JsonResource
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
            'name' => $this->name,
            'year' => (int) $this->year,
            'month' => (int) $this->month,
            'user_id' => (int) $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'subproject_id' => (int) $this->subproject_id,
            'subproject' => new SubprojectResource($this->whenLoaded('subproject')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
