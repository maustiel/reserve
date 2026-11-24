<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = ['inventory_code', 'category_id', 'name', 'condition', 'location', 'default_accessories', 'notes'];

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<Loan, $this> */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /** @return HasOne<Loan, $this> */
    public function openLoan(): HasOne
    {
        return $this->hasOne(Loan::class)->whereIn('status', ['requested', 'active']);
    }
}
