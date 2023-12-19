<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id','recipient_id','disburse_amount','frequency_id','payment_date','total_month','total_year','end_date'
    ];

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
