<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefBank extends Model
{
    use HasFactory;

    const AMBANK = 1;
    const AL_RAJHI_BANK = 2;
    const ALLIANCE = 3;
    const AFFIN_BANK = 4;
    const AGRO_BANK = 5;
    const BANK_OF_CHINA = 6;
    const BANK_MUAMALAT = 7;
    const BANK_ISLAM = 8;
    const BANK_RAKYAT = 9;
    const BANK_SIMPANAN_NASIONAL = 10;
    const BANK_OF_AMERICA = 11;
    const MUFG_BANK = 12;
    const BNP_PARIBAS = 13;
    const CIMB_BANK = 14;
    const CITI_BANK = 15;
    const DEUTSCHE_BANK = 16;
    const HONG_LEONG_BANK = 17;
    const HSBC_BANK = 18;
    const INDUSTRIAL_AND_COMMERCIAL_BANK_OF_CHINA = 19;
    const JP_MORGAN_CHASE = 20;
    const KUWAIT_FINANCE_HOUSE = 21;
    const MAYBANK = 22;
    const MIZUHO_BANK = 23;
    const OCBC_BANK = 24;
    const PUBLIC_BANK = 25;
    const RHB_BANK = 26;
    const STANDARD_CHARTERED_BANK = 27;
    const SUMITOMO_MITSUI_BANKING_CORPORATION = 28;
    const THE_ROYAL_BANK_OF_SCOTLAND = 29;
    const UNITED_OVERSEAS_BANK = 30;
    const CHINA_CONSTRUCTION_BANK = 31;
    const BANGKOK_BANK = 32;
    const MBSB = 33;

    protected $fillable = [
        'name',
        'bnm_code',
        'logo',
    ];

    public function receiver()
    {
        return $this->hasMany(Receiver::class);
    }

    public function allocation()
    {
        return $this->hasMany(Allocation::class);
    }

    public function allocationWaitingApproval()
    {
        return $this->hasMany(allocationWaitingApproval::class);
    }

    public function sender()
    {
        return $this->hasMany(SenderBankAccount::class);
    }
}
