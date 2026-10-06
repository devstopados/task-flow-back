<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Task
 */
class TaskResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'start_date' => $this->start_date?->toISOString(),
            'end_date' => $this->end_date?->toISOString(),
            'hours' => $this->hours !== null ? (float) $this->hours : null,
            'branch' => $this->branch,
            'link' => $this->link,
            'status_id' => $this->status_id,
            'status' => new StatusTaskResource($this->whenLoaded('status')),
            'subproject_id' => $this->subproject_id,
            'subproject' => new SubprojectResource($this->whenLoaded('subproject')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
