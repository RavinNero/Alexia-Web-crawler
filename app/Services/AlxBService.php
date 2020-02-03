<?php

namespace App\Services;

use App\Imobiliaria;
use App\Link;
use App\Anuncio;
use App\Image;
use App\Repositories\AlxBRepository;
use Illuminate\Support\Str;
use App\Endereco;

/*
*
*
*Este Service serve para guaradar os dados no banco de dados
*
*
 */

class AlxBService
{

	public function __construct(AlxBRepository $model, Link $link)
	{
        $this->model = $model;
        $this->link = $link;
	}

	public function getStringBetween($site, $start, $end)
	{
        $site = ' ' . $site;
        $ini = strpos($site, $start);
        if ($ini == 0)
        {
        	return '';
        }

        $ini += strlen($start);
        $len = strpos($site, $end, $ini) - $ini;
        //return substr($site, $ini, $len);
        return substr($site, $ini, $len);
   	}


    //Inicio da função para guardar os dados de cada anuncio ///////////////////////////////////
	public function storeData($data, $link_id)
	{
        //Variavel para pegar tipo negociacao do anuncio baseado no id do link do lopping
        $link = $this->link->where('id', $link_id)->first();

        switch ($data['type'])
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

        ///////////////////////////////
 
        switch ($data['usage']) 	
        {
            case 'residencial':
                $usage = 1;
                break;

            case 'comercial':
                $usage = 2;
                break;

            case 'indefinido/nao_interessa':
                $usage = 1;
                break;
        }
        
        //////////////////////////////
        
        /*switch ($link->negotiation)
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
            
            case 'Compra':
                $negotiation = 1;
                break;
            
            case 'compra':
                $negotiation = 1;
                break;
        }*/
        

        //
        //
        //Fim da lógica para transformar os tipos de imoveis e usos em seus respectivos valores numéricos
        //
        ////////////////////////////////////////////////////////

        //Codigo na url referente ao imovel ////////////////////////////////////

        $codigoLink = $link->link;
        $code = explode('/', $codigoLink)[3];

        $suites = $data['suites']?? 0;

        if(preg_match('/a/', $suites))
        {
        	$suites = explode('a', $data['suites']);

	        if($suites[0] > $suites[1])
	        {
	            $suites = trim($suites[0]);
	        
	        }else
	        {
	        	$suites = trim($suites[1]);
	        
	        }
        }


        
        $area_util = $data['area_util'];
        
        if(preg_match('/a/', $area_util))
        {
        	$area_util = str_replace('m²', '', explode('a', $data['area_util']));

	        if($area_util[0] > $area_util[1])
	        {
	           $area_util = trim($area_util[0]);

	        }else
	        {
	            $area_util = trim($area_util[1]);
	        }
        }

        

        $area_total = $data['area_total'];
        
        if(preg_match('/a/', $area_total))
        {
        	$area_total = str_replace('m²', '', explode('a', $data['area_total']));
	     
	        if($area_total[0] > $area_total[1])
	        {
	           $area_total = trim($area_total[0]);

	        }else
	        {
	            $area_total = trim($area_total[1]);
	        }
        }
        
        ////////////////////////////////////////////////////////////////////////

    	$null = null;

    	$bedrooms = $data['quartos'] ?? null;

    	$suites = $suites ?? 'nao informado';

    	$bathrooms = $data['banheiros'] ?? 'nao informado';

    	$parking = $data['vagas'] ?? 'nao informado';

    	$area_util = $area_util ?? 'nao informado';

    	$area_total = $area_total ?? 'nao informado';

    	$valor = $data['valor'] ?? 'nao informado';

    	$valor_aluguel = $data['valor_aluguel'] ?? 'nao informado';

    	$iptu = $data['iptu'] ?? 'nao informado';

    	$title = $data['titulo'];

    	?><pre><?php //var_dump($data['titulo']); ?></pre><?php

    	$slug = Str::slug($title);

    	$description = $data['descricao'] ?? '-';

		//dd($data);
        $anuncio = new Anuncio;
        
		$anuncio->link_id = $link->id;
		$anuncio->usage = $usage;
		$anuncio->type = $type;
		$anuncio->imobiliaria_id = $link->imobiliaria_id;
		$anuncio->negotiation = $link->negotiation;
		$anuncio->bedrooms = $bedrooms;
		$anuncio->suites = $suites;
		$anuncio->bathrooms = $bathrooms;
		$anuncio->parking = $parking;
		$anuncio->area_util = $area_util;
		$anuncio->area_total = $area_total;
		$anuncio->valor = $valor;
		$anuncio->valor_aluguel = $valor_aluguel;
		$anuncio->iptu = $iptu;
		$anuncio->title = $title;
		$anuncio->endereco = $data['endereco'];
		$anuncio->description = $description;
		$anuncio->save();

		if(count($data['images']) > 0)
		{
	        foreach ($data['images'] as $imgName)
	        {
	        	$img = new Image;

	        	$img->anuncio_id = $anuncio->id;
	        	$img->name = $imgName;

	        	$img->save();
	        }
		}


        /*$endereco = explode(' - ', $data['endereco']);
        $bairro = $endereco[0];

        $cidade = explode('/', $endereco[1])[0];
        $uf = explode('/', $endereco[1])[1];*/
       

        return true;/**/

	}//Fim da função para guardar os dados de cada anuncio ///////////////////////////////////
}