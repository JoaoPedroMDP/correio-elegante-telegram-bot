<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ecomper extends Model
{
    protected $fillable = ['fakeIdentifier', 'username', 'chat_id'];
}
