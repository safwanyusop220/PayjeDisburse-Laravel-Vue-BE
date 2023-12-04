<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    const ACTIVITY_CREATED = 1;
    const ACTIVITY_UPDATED = 2;
    const ACTIVITY_DELETED = 3;
    const ACTIVITY_RECOMMENDED = 4;
    const ACTIVITY_APPROVED = 5;
    const ACTIVITY_REJECTED = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function audit_trail()
    {
        return $this->hasMany(AuditTrail::class);
    }

    public function log($message, $model=null)
    {
        $message = ucwords($message);
        $now = Carbon::now('Asia/Kuala_Lumpur');

        $data = [
            'user_id'     => $this->id,
            'name'        => $this->name,
            'date'        => strval($now->format('d/m/Y')),
            'time'        => strval($now->format('h:i A')),
            'activity_id' => "$message",
            'model'       => $model
        ];

        AuditTrail::query()->create($data);

    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
