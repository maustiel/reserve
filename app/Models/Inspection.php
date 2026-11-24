<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    protected $fillable = ['loan_id', 'type', 'condition', 'accessories', 'notes', 'photo_path', 'borrower_signed', 'manager_signed', 'recorded_by'];
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
