<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AlxService as Service;
use DOMDocument;
use Curl;

/**
 *
 *
 *
 * 
 *Esse robo é responsável por pegar os links passados pelo usuário, filtrar apenas os links que queremos e armazenalos em um banco de dados atrvéz de um service chamado AlxAService
 *
 *
 *
 * 
 */

class AlxAController extends Controller
{
	protected $service;

	public function __construct(Service $service)
	{
        $this->service = $service;
	}

    public function find(Request $request)
    {
        /*
        
	        Aqui seram passados dois parametros o link inicial e o final da paginação;
	        Será especificado qual a area do link que identifica a paginação
	        Este valor será alterado em cada um dos loops
	        Então é necessário especificar qual falor será alterado pelo numero do loop

        */
        
		//Delimiter tells to the robot where have to change the page number
		$urlDelimiter = '/'.'pagina=73'.'/';
        $delimiter = $urlDelimiter;

        //Here is where is filter the number of pages that have to be parsed
    	$number_paginate = str_replace(str_split('\\-./'), "", filter_var($urlDelimiter, FILTER_SANITIZE_NUMBER_INT));


    	for ($i=1; $i <= $number_paginate; $i++)
    	{
    	    //This code is able to change the exactely number of paginate
			$url_changed = str_replace(filter_var($urlDelimiter, FILTER_SANITIZE_NUMBER_INT), $i, 'http://www.imobiliariafigueira.com.br/imoveis/a-venda?pagina=73');

	        ///////////////////////////////////////////////////////////////////////////////
	        //
	        //Seletor de links
	        //
	        ///////////////////////////////////////////////////////////////////////////////

			//Get the page's HTML source using file_get_contents.
			$html = file_get_contents($url_changed);
			 
			//Instantiate the DOMDocument class.
			$htmlDom = new DOMDocument;
			 
			//Parse the HTML of the page using DOMDocument::loadHTML
			@$htmlDom->loadHTML($html);
			 
			//Extract the links from the HTML.
			$links = $htmlDom->getElementsByTagName('a');
			 
			//Array that will contain our extracted links.
			$extractedLinks = array();
			 
			//Loop through the DOMNodeList.
			//We can do this because the DOMNodeList object is traversable.
			foreach($links as $link)
			{
			    //Get the link text.
			    $linkText = $link->nodeValue;
			    //Get the link in the href attribute.
			    $linkHref = $link->getAttribute('href');
			 
			    //If the link is empty, skip it and don't
			    //add it to our $extractedLinks array
			    if(strlen(trim($linkHref)) == 0)
			    {
			        continue;
			    }
			 
			    //Skip if it is a hashtag / anchor link.
			    if($linkHref[0] == '#')
			    {
			        continue;
			    }

			    //Add the link to our $extractedLinks array.
			    $extractedLinks[] = array(
			        'text' => str_replace("\t", "", str_replace("\n", "", str_replace(str_split('\\'), "", (string) $linkText))),
			        'href' => str_replace("\t", "", str_replace("\n", "", str_replace(str_split('\\'), "", (string) $linkHref)))
			    );
			 
			}
	        
	        //Variables used to make a right surch for the required links
			$termo = '~/'.'imovel'.'/~';
			$termo2 = '~/'.'venda'.'/~';

			//here is the VALIDATOR, where the robot surch for a determinated link 
			
			foreach ($extractedLinks as $extractedLink)
			{
				if 
				(
					preg_match($termo, $extractedLink['href'])
					or
					preg_match($termo2, $extractedLink['href'])
				)
				{
					$this->service->store($extractedLink['href'], 'Imobiliária Figueiras', 'compra');
				}
			}
	    }

        
	}

}


