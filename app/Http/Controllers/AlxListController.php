<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AlxBrepository;
use App\Imobiliaria;
use App\Anuncio;
use Illuminate\Support\Str;
use App\Link;
use DB;

//Conectar um banco antigo para receber os dados como eles devem ser;
//Apagar os dados antigos para ficar apenas os novos
//Cadastrar direto neles

class AlxListController extends Controller
{
    protected $model;

    public function __construct(Imobiliaria $model, Anuncio $anuncio, Link $link)
    {
        $this->model = $model;
        $this->anuncio = $anuncio;
        $this->link = $link;
    }

    public function listData()
    {
        
        $data = $this->anuncio->where('imobiliaria_id', 1)->get();

        foreach ($data as $anuncio)
        {
        	foreach ($anuncio->images as $image) 
        	{
        		$images[] = array($image);
        	}/**/

            ////////////////////////////////////////////////////////
            //
            //Lógica para transformar os tipos de imoveis e usos e negociações em seus respectivos valores numericos
            //
            //

            switch ($anuncio->type) 
            {
                case 'Apartamento':
                    $type = 1;
                    break;

                case 'Casa':
                    $type = 2;
                    break;

                case 'Chácara':
                    $type = 3;
                    break;

                case 'Casa de Condomínio':
                    $type = 4;
                    break;

                case 'Flat':
                    $type = 5;
                    break;

                case 'Lote / Terreno':
                    $type = 6;
                    break;

                case 'Sobrado':
                    $type = 7;
                    break;

                case 'Cobertura':
                    $type = 8;
                    break;

                case 'Kitnet':
                    $type = 9;
                    break;

                case 'Consultório':
                    $type = 10;
                    break;

                case 'Edifício Residencial':
                    $type = 11;
                    break;

                case 'Sala Comercial':
                    $type = 12;
                    break;

                case 'Fazenda / Sítio':
                    $type = 13;
                    break;

                case 'Galpão / Depósito / Armazém':
                    $type = 14;
                    break;

                case 'Imóvel Comercial':
                    $type = 15;
                    break;

                case 'Loja':
                    $type = 16;
                    break;

                case 'Lote / Terreno':
                    $type = 17;
                    break;

                case 'Ponto Comercial':
                    $type = 18;
                    break;
            }



            switch ($anuncio->usage) 	
            {
	            case 'residencial':
	                $usage = 1;
	                break;

	            case 'comercial':
	                $usage = 2;
	                break;
            }




            switch ($anuncio->usage) 
            {
	            case 'residencial':
	                $usage = 1;
	                break;

	            case 'comercial':
	                $usage = 2;
	                break;
            }


            

            switch ($anuncio->link->negotiation) 
            {
	            case 'Venda':
	                $negotiation = 1;
	                break;

	            case 'venda':
	                $negotiation = 1;
	                break;

	            case 'Aluguel':
	                $negotiation = 2;
	                break;

	            case 'aluguel':
	                $negotiation = 2;
	                break;

	            case 'Venda Aluguel':
	                $negotiation = 3;
	                break;

	            case 'venda aluguel':
	                $negotiation = 3;
	                break;
            }/**/

            //
            //
            //Fim da lógica para transformar os tipos de imoveis e usos em seus respectivos valores numéricos
            //
            ////////////////////////////////////////////////////////

            //Codigo na url referente ao imovel ////////////////////////////////////

            $codigoLink = $anuncio->link->link;
            $code = explode('/', $codigoLink)[3];

            
            ////////////////////////////////////////////////////////////////////////
            
            $null = 'bolo';
 
        	$bedrooms = $anuncio->bedrooms ?? null;

        	$suites = $anuncio->suites ?? null;

        	$bathrooms = $anuncio->bathrooms ?? null;

        	$parking = $anuncio->parking ?? null;

        	$area_util = $anuncio->area_util ?? null;

        	$area_total = $anuncio->area_total ?? null;

        	$valor = $anuncio->valor ?? '0';

        	$valor_aluguel = $anuncio->valor_aluguel ?? '0';

        	$iptu = $anuncio->iptu ?? '0';

        	$title = $anuncio->title ?? null;

        	$slug = $null;

        	$description = $anuncio->description ?? null;

        	$created_at = $anuncio->created_at;

        	$updated_at = $anuncio->updated_at;

        	$null = null;


            //insere os dados nas tabelas de testes
        	$anuncioData = DB::insert('insert into anunciostestes.anuncios(user_id, usage_type_id, type_id, condominio_id, negotiation_id, new_immobile, exchange, bedrooms, suites, bathrooms, parking, area_util, area_total, valor, valor_aluguel, condominio_mes, iptu, codigo, title, slug, description, status, deleted, created_at, updated_at)
       	values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
       	    [
       	    	1,
				$usage,
				$type,
				0,
				$negotiation,
				0,
				0, 
				$bedrooms, 
				$suites, 
				$bathrooms, 
				$parking, 
				$area_util, 
				$area_total, 
				$valor, 
				$valor_aluguel, 
				null, 
				$iptu, 
				$code, 
				$title, 
				$slug, 
				$description, 
				'ativado', 
				0,
				$created_at,
        	    $updated_at
       	    ]);/**/

       	    $anuncio_id = DB::select('select id from anunciostestes.anuncios where codigo = ? and slug = ? and title = ?', [$code, $slug, $title]);
            ?><pre><?php var_dump($anuncio->endereco) ?></pre><?php

            
            $endereco = explode(' - ', $anuncio->endereco);
            $bairro = $endereco[0];
            $cidade = explode('/', $endereco[1])[0];
            $uf = explode('/', $endereco[1])[1];
            
				DB::insert('insert into anunciostestes.anuncio_enderecos(anuncio_id, mostrar_endereco, cep, cidade, slug_cidade, uf, bairro, slug_bairro, logradouro, numero, show_number_site, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?)', [
	                $anuncio_id[0]->id,
	                0,
	                000000-000,
                    $cidade,
                    Str::slug($cidade),
                    $uf,
                    $bairro,
                    Str::slug($bairro),
                    '',
                    's/n',
                    0,	
                    $created_at,
                    $updated_at
	            ]);/**/
        
	        /*foreach ($images as $image) 
	        {
	        	foreach ($image as $img)
	        	{

	        	    ?><pre><?php 
	        	    $imageData = DB::insert('insert into anunciostestes.anuncio_images(
	        	    	anuncio_id,
	        	    	name, 
	        	    	created_at, 
	        	    	updated_at)
	        	    	values 
	        	    	(?, ?, ?, ?)',
	        	    	[
		        	    	$anuncio_id,
		        	    	$img->name,
		        	    	$created_at,
		        	    	$created_at
	        	    	]);
	        	    ?></pre><?php

	        	}
	        	
	        	
	        }*/

        }

        /*return view('list', compact('data'))true;*/
    }

}
    
