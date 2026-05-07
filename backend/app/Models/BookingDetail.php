<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;

class BookingDetail extends Model
{
    protected $table = 'BookingDetails';

    protected $primaryKey = 'ID';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'ID',
        'BookingID',
        'TicketID',
        'Quantity',
        'Price'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'BookingID');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'TicketID');
    }
}