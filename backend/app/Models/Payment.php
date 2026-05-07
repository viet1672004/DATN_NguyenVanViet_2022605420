<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    const STATUS_PAID = 'PAID';
    const STATUS_PENDING = 'PENDING';
    const STATUS_FAILED = 'FAILED';

    protected $table = 'Payments';

    protected $primaryKey = 'ID';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'ID',
        'BookingID',
        'PaymentMethod',
        'Amount',
        'PaymentStatus',
        'PaymentDate',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'BookingID');
    }
}