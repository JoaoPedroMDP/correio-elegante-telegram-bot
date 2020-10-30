<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ecomper extends Model
{
    protected $fillable = ['name', 'fakeIdentifier', 'username', 'chat_id'];
}
