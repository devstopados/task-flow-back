<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['code', 'name', 'start_date', 'end_date', 'hours', 'branch', 'link', 'status_id', 'subproject_id'])]
class Task extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'hours' => 'decimal:2',
            'status_id' => 'integer',
            'subproject_id' => 'integer',
        ];
    }

    /**
     * Get the subproject that owns the task.
     *
     * @return BelongsTo<Subproject, $this>
     */
    public function subproject(): BelongsTo
    {
        return $this->belongsTo(Subproject::class);
    }
}
