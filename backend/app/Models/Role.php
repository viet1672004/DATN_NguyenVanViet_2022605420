<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'Roles';

    protected $primaryKey = 'ID';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'ID',
        'RoleName'
    ];
}