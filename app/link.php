<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class link extends Model
{
    protected $table = 'links';

    public function imobiliaria(){
    	return $this->belongsToMany('App\Imobiliaria');
    }

    public function anuncio(){
    	return $this->belongsTo('App\Anuncio');
    }
}
