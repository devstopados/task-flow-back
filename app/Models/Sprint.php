<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'subproject_id', 'name', 'year', 'month'])]
class Sprint extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'subproject_id' => 'integer',
            'year' => 'integer',
            'month' => 'integer',
        ];
    }

    /**
     * Get the user that owns the sprint.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subproject that owns the sprint.
     *
     * @return BelongsTo<Subproject, $this>
     */
    public function subproject(): BelongsTo
    {
        return $this->belongsTo(Subproject::class);
    }
}
