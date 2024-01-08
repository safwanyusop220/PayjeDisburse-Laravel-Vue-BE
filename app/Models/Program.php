<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
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
        'status_id',  'recommend_by_id', 'recommend_date', 'approved_by_id', 'approved_date', 'reason_to_reject',
        'rejected_by_id', 'rejected_date',
    ];

    public function created_by()
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

    public function type()
    {
        return $this->belongsTo(ProgramType::class, 'type_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
    
    public function bankPanel()
    {
        return $this->belongsTo(BankPanel::class, 'bank_panel');
    }

    public function frequency()
    {
        return $this->belongsTo(Frequency::class, 'frequency_id');
    }

    public function installmentPrograms()
    {
        return $this->hasMany(InstallmentProgram::class, 'program_id');
    }

    public function recipients()
    {
        return $this->hasMany(Receipient::class, 'program_id');
    }

    public function recipientPrograms()
    {
        return $this->hasMany(RecipientProgram::class, 'program_id');
    }
}
