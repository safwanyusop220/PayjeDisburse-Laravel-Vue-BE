<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualSchedularRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_id', 'program_id', 'payment_date', 'total_month', 'total_year',
    ];

    public function recipient()
    {
        return $this->belongsTo(Receipient::class, 'recipient_id');
    }
}
