<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipientProgram extends Model
{
    use HasFactory;
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

    protected $fillable = ['recipient_id', 'program_id', 'status_id', 'created_by_id', 'recommend_by_id', 'recommend_date', 'approved_by_id', 'approved_date', 'rejected_by_id', 'rejected_date', 'reason_to_reject'];

    public function recipient()
    {
        return $this->belongsTo(Receipient::class, 'recipient_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

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

    public function rejected_by()
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }
}
