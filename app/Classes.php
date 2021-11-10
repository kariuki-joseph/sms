<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{

    protected $fillable=[
        'name','capacity','class_teacher_id'
    ];

    public function students(){
        return $this->hasMany(Students::class,'class_id', 'id');
    }
    public function classTeacher(){
        return $this->hasOne(Teachers::class, 'id', 'class_teacher_id');
    }
}
