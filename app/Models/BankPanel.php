<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankPanel extends Model
{
    use HasFactory;

    public function bank()
    {
        return $this->belongsTo(RefBank::class, 'bank_id');
    }
}
