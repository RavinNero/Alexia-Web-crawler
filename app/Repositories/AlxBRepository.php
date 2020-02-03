<?php

namespace App\Repositories;

use App\Imobiliaria;
use App\Link;

class AlxBRepository
{

	protected $imobiliaria;
    protected $link;

	public function __construct
	(
		Imobiliaria $imobiliaria,
		Link $link
	)
	{
        $this->imobiliaria = $imobiliaria;
        $this->link = $link;
	}

    public function findLinksByName($name)
    {
    	$imob_id = $this->imobiliaria->where('name', $name)->first()->id;
    	$links = $this->link->where('imobiliaria_id', $imob_id)->get();

		return $links;

    }

    
    
} 

