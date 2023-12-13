<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipient extends Model
{
    use HasFactory, SoftDeletes;
    const STATUS_RECOMMENDED = 2;
    const STATUS_APPROVE = 3;
    const STATUS_REJECT = 4;

    const ACTIVITY_CREATED = 1;
    const ACTIVITY_UPDATED = 2;
    const ACTIVITY_DELETED = 3;
    const ACTIVITY_RECOMMENDED = 4;
    const ACTIVITY_APPROVED = 5;
    const ACTIVITY_REJECTED = 6;

    protected $fillable = [
        'status_id', 'reason_to_reject'
    ];

    public function bank()
    {
        return $this->belongsTo(RefBank::class, 'bank_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function frequency()
    {
        return $this->belongsTo(Frequency::class, 'frequency_id');
    }

    public function individualRecipient()
    {
        return $this->hasOne(IndividualRecipient::class, 'recipient_id');
    }
}
