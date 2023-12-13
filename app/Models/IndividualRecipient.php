<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualRecipient extends Model
{
    use HasFactory;

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function recipient()
    {
        return $this->belongsTo(Receipient::class, 'recipient_id');
    }

    public function frequency()
    {
        return $this->belongsTo(Frequency::class, 'frequency_id');
    }

    public function schedular()
    {
        return $this->hasMany(IndividualSchedularRecipient::class, 'recipient_id');
    }
}
