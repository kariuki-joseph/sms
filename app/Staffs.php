<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staffs extends Model
{
    protected $fillable = [
        'name','birth_certificate_number','id_number','phone','email','staff_type_id',
        'kra','gross_salary','nhif','tsc','location','passport','id_card','birth_certificate'
    ];

    public function staffTypes(){
        return $this->hasMany(UserType::class, 'id', 'staff_type_id');
    }
    public function staffType(){
        return $this->hasOne(UserType::class, 'id', 'staff_type_id');
    }
    public function documents(){
        return $this->morphMany(Documents::class, 'documentable');
    }
}
