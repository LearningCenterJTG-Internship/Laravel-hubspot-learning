<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        "name",
        "domain",
        "city",
        "industry",
        "address",
        "phone",
        "state",
        "lifecyclestage",
        "company_id"
    ];
}
