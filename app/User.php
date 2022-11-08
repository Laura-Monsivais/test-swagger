<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table       = 'users';
    protected $primaryKey  = 'id';


    protected $fillable = [
        'email', 'password','person_id','user', 'rol', 'isActive'
    ];

    protected $hidden = [
        'password'
    ];

    public function person()
    {
        return $this->belongsTo('App\Person');
    }
}
