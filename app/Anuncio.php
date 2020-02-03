<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anuncio extends Model
{

    public function images(){
    	return $this->hasMany('App\Image', 'id');
    }

    public function link(){
    	return $this->belongsTo('App\Link');
    }

    public function endereco(){
    	return $this->hasOne('App\Endereco', 'id');
    }
}
