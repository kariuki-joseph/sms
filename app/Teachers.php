<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teachers extends Model
{
    protected $fillable = [
        'name','email','phone'
    ];

    public function roles(){
        return $this->hasMany(Roles::class, 'user_id', 'id');
    }
    public function class(){
        return $this->hasOne(Classes::class, 'class_teacher_id', 'id');
    }
}
