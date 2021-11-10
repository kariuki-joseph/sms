<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Houses extends Model
{
    protected $fillable = [
        'name', 'capacity'
    ];

}
