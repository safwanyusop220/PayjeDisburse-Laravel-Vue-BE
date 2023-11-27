<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipient extends Model
{
    use HasFactory;
    const STATUS_RECOMMENDED = 2;
    const STATUS_APPROVE = 3;
    const STATUS_REJECT = 4;

    protected $fillable = [
        'status_id'
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
}
