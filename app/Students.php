<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    protected $fillable = [
        'name','adm_number','gender','dob','location','class_id','previous_school','admission_class',
        'birth_cert_number','nemis_number','medical'
        ];


    public function class()
    {
        return $this->hasOne(Classes::class,'id','class_id');
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'user_id', 'id');
    }
    function parents(){
        return $this->hasOne(Parents::class, 'student_id', 'id');
    }

    public function fees(){
        return $this->hasMany(Fees::class, 'adm_number', 'id');
    }

    public function documents(){
        return $this->morphMany(Documents::class, 'documentable');
    }
}
