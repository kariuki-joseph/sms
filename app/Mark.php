<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $fillable = [
        'min_score','max_score','grade','remarks'
    ];

    public function grade(){

    }
}
