<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'Users';        // 👈 Tên bảng đúng
    protected $primaryKey = 'ID';      // 👈 PK là ID
    public $incrementing = false;      // 👈 vì GUID
    protected $keyType = 'string';     // 👈 GUID là string

    protected $fillable = [
        'ID',
        'Name',
        'Email',
        'Password',
        'Phone',
        'RoleID',
        'Status'
    ];

    protected $hidden = [
        'Password'
    ];

    // Status: 1 = active, 0 = blocked
    public function getIsBlockedAttribute()
    {
        return $this->Status == 0 ? 1 : 0;
    }

    protected $appends = ['IsBlocked'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'UserID');
    }
}