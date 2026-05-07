<?php

namespace App\Models;

use App\Models\BookingDetail;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'Tickets';

    protected $primaryKey = 'ID';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'TicketName',
        'Price',
        'ParkID',
        'Description',
        'TicketType',
        'NumberOfDays',
        'ComboInfo',
        'Status'
    ];

    public function park()
    {
        return $this->belongsTo(Park::class, 'ParkID', 'ID');
    }

    public function bookingDetails()
    {
        return $this->hasMany(
            BookingDetail::class,
            'TicketID',
            'ID'
        );
    }
}