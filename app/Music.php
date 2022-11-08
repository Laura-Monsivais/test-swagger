<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    protected $table       = 'music';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'name', 'author','person_id'
    ];

    public function person()
    {
        return $this->belongsToMany('App\Person');//An music has many persons
    } 
}
