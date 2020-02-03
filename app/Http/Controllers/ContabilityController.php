<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Anuncio;
use App\Endereco;

class ContabilityController extends Controller
{
    protected $dados;
    protected $anuncio;
    protected $endereco;

    public function __construct(Anuncio $anuncios)
    {
    	$this->anuncios = $anuncios->all();
    	$this->dados = DB::select('select * from anuncios');
    	$this->endereco = DB::select('select * from anuncio_enderecos');
    }

    public function cont()
    {
    	foreach ($this->anuncios as $anuncio) 
    	{
    		//dd(count($this->dados));
    		//dd($anuncio->id);
    		
    		$id = $anuncio->id + 3636;

    		print_r($id);
    		echo '<br/>';

    		return DB::update('update anuncio_enderecos set anuncio_id = ? where anuncio_id = ?',
       	    [
       	        $id,
       	        $anuncio->id
       	    ]);

    	}
       
    }
}
