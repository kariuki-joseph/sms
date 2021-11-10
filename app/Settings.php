<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'sch_name', 'sch_motto','sch_vission','sch_mission', 'logo', 'email','po_address', 'phone'
    ];
}
