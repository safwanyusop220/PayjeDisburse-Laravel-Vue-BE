<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    const STATUS_RECOMMENDED = 2;
    const STATUS_APPROVE = 3;
    const STATUS_REJECT = 4;

    protected $fillable = [
        'status_id'
    ];

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
}
