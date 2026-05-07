<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'Bookings';

    protected $primaryKey = 'ID';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'ID',
        'UserID',
        'ParkID',
        'BookingDate',
        'BookingDateTime',
        'TotalPrice',
        'Status',
        'BookingCode'
    ];

    public function details()
    {
        return $this->hasMany(BookingDetail::class, 'BookingID');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->BookingCode = self::generateCode();
        });
    }

    public static function generateCode()
    {
        $date = now()->format('Ymd');

        do {
            $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
            $code = "BK{$date}{$random}";
        } while (self::where('BookingCode', $code)->exists());

        return $code;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    public function park()
    {
        return $this->belongsTo(Park::class, 'ParkID');
    }
}