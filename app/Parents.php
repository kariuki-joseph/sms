<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    protected $fillable = [
       'student_id','mother_name','mother_contact','father_name','father_contact','guardian_name','guardian_contact'
    ];

    public function students(){
        return $this->hasMany(Students::class, 'student_id', 'id');
    }
    public function documents(){
        return $this->morphMany(Documents::class, 'documentable');
    }
}
