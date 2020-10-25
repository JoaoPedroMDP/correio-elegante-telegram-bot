<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable= ['sender', 'receiver', 'text', 'message_id'];
}
