<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $table = 'ChatHistories';

    protected $primaryKey = 'ID';

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'CreatedAt';

    const UPDATED_AT = 'UpdatedAt';

    protected $fillable = [
        'ID',
        'UserID',
        'Message',
        'Reply',
        'Intent'
    ];
}