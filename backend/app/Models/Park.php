<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Park extends Model
{
    protected $table = 'Parks'; 

    protected $primaryKey = 'ID'; 

    public $timestamps = true;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'ParkName',
        'Location',
        'Description',
        'OpenTime',
        'CloseTime',
        'Image',
        'Status'
    ];

    public function tickets()
    {
        return $this->hasMany(
            Ticket::class,
            'ParkID',
            'ID'
        );
    }

    public function bookings()
    {
        return $this->hasMany(
            Booking::class,
            'ParkID',
            'ID'
        );
    }
}