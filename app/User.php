<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','password', 'bio','type'

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //relationship
    public function parent(){
        return $this->hasOne(Parents::class, 'user_id');
    }

    public function parents(){
        return $this->hasMany(Parents::class, 'user_id');
    }
    public function userType(){
        return $this->hasOne(UserType::class, 'user_type_id');
    }
    public function userTypes(){
        return $this->hasMany(UserType::class, 'user_type_id');
    }
}
