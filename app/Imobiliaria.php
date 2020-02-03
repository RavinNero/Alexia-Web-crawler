<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Imobiliaria extends Model
{
    protected $table = 'imobiliarias';
    protected $primarykey = 'id';

    public function links(){
        return $this->hasMany('App\Link', 'id');
    }

}
