<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubspotToken extends Model
{
    protected $fillable = [
        'access_token',
        'refresh_token'
    ];
}
