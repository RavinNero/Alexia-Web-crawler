<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    protected $table = 'anuncio_enderecos';

    public function anuncio()
    {
    	return $this->belongsTo('App\Anuncio');
    }
}
