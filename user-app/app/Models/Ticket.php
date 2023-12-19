<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        "hs_pipeline",
        "hs_pipeline_stage",
        "hs_ticket_priority",
        "subject",
        "ticket_id"
    ];
}
