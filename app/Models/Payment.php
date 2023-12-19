<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    const STATUS_REQUEST = 5;
    const STATUS_PROCESSING = 6;
    const STATUS_PROCEED = 7;

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    // public function recipients(){
    //     return $this->hasManyThrough(Receipient::class, Program::class);
    
    // }
}
