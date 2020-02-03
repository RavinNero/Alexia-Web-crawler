<?php

namespace App\Services;

use App\Imobiliaria;
use App\Link;

/*
*
*
*Este Service serve para guaradar os dados no banco de dados
*
*
 */

class AlxService
{
	//Houveram alguns problemas no desenvolver desse service que eu tive que contornar para resolver
	//Foi nececessário colocar um if que valida se a imobiliaria já está cadastrada, pois é ncessário que seja cadastrado apenas uma imobiliaria e varios links ligados a ela
	//Foi necessária uma vilidação, para não serem cadastrados duas vezes o mesmo numero, primeiro pegamos o falor a ser armazenado e então verificamos se o que é retornado é igual a null, se for nós guardamos o link
	
	protected $imob;

    public function __construct(Imobiliaria $imob)
    {
		$this->imob = $imob;
    }

    //Inicio da função para guardar links ///////////////////////////////////
	public function store($link, $name, $negotiation)
	{   
		$imobiliaria = new Imobiliaria;
        

        //valida se a imobiliária já está cadastrada, se não estiver ele armazena o nome dela e o link do loop
		if ($this->imob->where('name', $name)->first() == null)
		{
		    $imobiliaria->name = $name;
		    $imobiliaria->save();

		    $links = new Link;

			$links->imobiliaria_id = $imobiliaria->id;
			$links->link = $link;
			$links->negotiation = $negotiation;
			$links->save();
		}
		else
		{
			$imobiliaria = new Imobiliaria;
            
            $imobiliaria_id = $imobiliaria->where('name', $name)->first();
			$links = new Link;
            
            //Aqui há uma validação para saber se o link já foi armazenado

            if($links->where('link', $link)->first() == false)
            {
				$links->imobiliaria_id = $imobiliaria_id->id;
				$links->link = $link;
			    $links->negotiation = $negotiation;
				$links->save();
            }
            
		}/**/

		return "Salvo com sucesso";
	}//Fim da função para guardar links ///////////////////////////////////
    
}