<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'Blogs';

    protected $primaryKey = 'ID';

    public $timestamps = true;

    protected $fillable = [
        'Title',
        'BannerImage',
        'Summary',
        'Content',
        'Tags',
        'ParkID',
        'UserID',
        'Status'
    ];

    public function park()
    {
        return $this->belongsTo(Park::class, 'ParkID');
    }
}