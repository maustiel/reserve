<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = ['inventory_code', 'category_id', 'name', 'condition', 'location', 'default_accessories', 'notes'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function openLoan()
    {
        return $this->hasOne(Loan::class)->whereIn('status', ['requested', 'active']);
    }
}
