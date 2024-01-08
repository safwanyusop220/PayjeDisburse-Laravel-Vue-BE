<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipient extends Model
{
    use HasFactory, SoftDeletes;
    const STATUS_SUBMITTED = 1;
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
        'status_id', 'reason_to_reject','name','identification_number','address','postcode','phone_number',
        'email','bank_id','account_number','program_id','recipient_id','disburse_amount','frequency_id','payment_date',
        'total_month','total_year', 'created_by_id', 'recommend_by_id', 'recommend_date', 'approved_by_id', 'approved_date',
        'rejected_by_id', 'rejected_date'
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

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function recommend_by()
    {
        return $this->belongsTo(User::class, 'recommend_by_id');
    }

    public function approved_by()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function rejected_by()
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    public function frequency()
    {
        return $this->belongsTo(Frequency::class, 'frequency_id');
    }

    public function individualRecipient()
    {
        return $this->hasOne(IndividualRecipient::class, 'recipient_id');
    }

    public function schedular()
    {
        return $this->hasMany(IndividualSchedularRecipient::class, 'recipient_id');
    }

    public function recipientProgram()
    {
        return $this->hasMany(RecipientProgram::class, 'recipient_id');
    }
}
