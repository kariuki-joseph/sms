<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    //
    protected $fillable = ['name','amount','upcoming'];
    
    public function payments(){
        return $this->hasMany(Fees::class);
    }
}
