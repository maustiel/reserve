<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'active' && $this->due_at->isPast();
    }
}
