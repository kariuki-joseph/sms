<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    protected $fillable = [
        'owner_id', 'name', 'url', 'user_type'
    ];

    public function documentable(){
        return $this->morphTo();
    }
}
