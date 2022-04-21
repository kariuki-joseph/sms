<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fees extends Model
{
    //

    
    public function student(){
        return $this->belongsTo(Students::class,'adm_number', 'adm_number');
    }

    public function payable(){
        return $this->belongsTo(Payable::class);
    }
}
