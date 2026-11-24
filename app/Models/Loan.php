<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = ['reference', 'equipment_id', 'user_id', 'status', 'purpose', 'requested_at', 'handed_over_at', 'due_at', 'returned_at', 'extended_once'];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'handed_over_at' => 'datetime',
            'due_at' => 'date',
            'returned_at' => 'datetime',
            'extended_once' => 'boolean',
        ];
    }

    /** @return BelongsTo<Equipment, $this> */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Inspection, $this> */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }
}
