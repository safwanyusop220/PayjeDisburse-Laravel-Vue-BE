<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentProgram extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount', 'name', 'payment_date', 'value'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
