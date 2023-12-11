<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankPanel extends Model
{
    protected $fillable = ['holder_name', 'bank_id', 'account_number'];
    use HasFactory, SoftDeletes;
    const ACTIVITY_CREATED = 1;
    const ACTIVITY_UPDATED = 2;
    const ACTIVITY_DELETED = 3;
    const ACTIVITY_RECOMMENDED = 4;
    const ACTIVITY_APPROVED = 5;
    const ACTIVITY_REJECTED = 6;


    public function bank()
    {
        return $this->belongsTo(RefBank::class, 'bank_id');
    }
}
