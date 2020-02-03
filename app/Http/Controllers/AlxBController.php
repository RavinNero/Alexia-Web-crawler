<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AlxBRepository;
use DOMDocument;
use DOMXPath;
use Curl;
use Carbon\Carbon;
use App\Services\AlxBService;
use DB;

/*

Esse robô pega os links armazenados no banco de dados, vai em cada um deles e pega os dados a serem armazenados pelo Service AlxBService

*/

class AlxBController extends Controller
{
    protected $model;
    protected $service;

    public function __construct(AlxBRepository $model, AlxBService $service)
    {
        $this->model = $model;
        $this->service = $service;
    }/**/

    public function dataExtractor()
    {
        $data_links = $this->model->findLinksByName('Imobiliária Figueiras');

        /*foreach ($data_links as $link) 
        {
            dd($link->negotiation);
        }*/

        foreach ($data_links as $link) 
        {
            $right_link = 'http://www.imobiliariafigueira.com.br' . $link->link;
        
            $negotiation = $link->negotiation/*'venda'*/;

            $html = file_get_contents($right_link);
            
            //http://www.imobiliariafigueira.com.br/empreendimento/edificio-marbella-apartamentos-centro-barretos/8897379-FIA
            //http://www.imobiliariafigueira.com.br/imovel/terreno-de-200-m-mais-parque-barretos-a-venda-por-33000/TE0858-FIA

            $htmlDom = new DOMDocument;
             
            //Parse the HTML of the page using DOMDocument::loadHTML
            @$htmlDom->loadHTML($html);

            $htmlDom->preserveWhiteSpace = false;
            
            //A partir da variavel $xpath será possível extrair os dados de cada anuncio 
            $xpath = new DOMXPath($htmlDom);
             
            //Array that will contain our extracted data.
            $extractedInformation = array();
            
            //Essa variavel armazena os dados da tabela que contem informações como banheiros, quartos, vagas etc.
            
            $contents = $xpath->query('//div[@class="item-info"]');//Query que muda de acordo com a estrutura do site
            
            ////////////////////////////////////////////////////////////////////////////
            ///
            ///
            ///
            ///
            ///
            ///
            ////////////////////////////////////////////////////////////////////////////

            $i = 0;
             
            foreach ($contents as $content)
            {
                ?><pre><?php //var_dump(trim($content->nodeValue)); ?></pre><?php

                $extractedInformation[] = array(
                    $i => str_replace("\t", "", str_replace("\n", "", str_replace(str_split('\\'), "", (string) $content->nodeValue)))
                );

                $i++;
            }

            //Esses if verificarão aonde deve entrar cada informação dentro do array de informações a serem guardadas
            for ($i=0; $i < count($extractedInformation); $i++) 
            {
                foreach ($extractedInformation[$i] as $info)
                {
                    
                    //if para quartos ///////////////////////
                    if
                    (
                        preg_match('/Quartos/', $info)
                        or
                        preg_match('/Quarto/', $info)
                    )
                    {
                        $informationToBeStored['quartos'] = trim(str_replace("Quarto", "", str_replace("Quartos", "", $info)));
                    }

                    //if para suites ///////////////////////
                    if 
                    (
                        preg_match('/Suítes/', $info)
                        or
                        preg_match('/Suíte/', $info)
                    )
                    {
                        $informationToBeStored['suites'] = trim(str_replace("Suíte", "", str_replace("Suítes", "", $info)));
                    }

                              
                    //if para banheiros ///////////////////////
                    if 
                    (
                        preg_match('/Banheiros/', $info)
                        or
                        preg_match('/Banheiro/', $info)
                    )
                    {
                        $informationToBeStored['banheiros'] = trim(str_replace("Banheiro", "", str_replace("Banheiros", "", $info)));
                    }

                              
                    //if para vagas ///////////////////////
                    if 
                    (
                        preg_match('/Vagas/', $info)
                        or
                        preg_match('/Vaga/', $info)
                    )
                    {
                        $informationToBeStored['vagas'] = trim(str_replace("Vaga", "", str_replace("Vagas", "", $info)));
                    }

                              
                    //if para area_util ///////////////////////
                    if 
                    (
                        preg_match('/Área útil/', $info)
                        or
                        preg_match('/Área construída/', $info)
                    )
                    {
                        $informationToBeStored['area_util'] = trim(str_replace('Área construída', '', str_replace(str_split('\\://'), "", (str_replace("Área útil", "", $info)))));
                    }

                              
                    //if para area_total ///////////////////////
                    if 
                    (
                        preg_match('/Área total/', $info)
                        or
                        preg_match('/Área do terreno/', $info)
                    )
                    {
                        $informationToBeStored['area_total'] = trim(str_replace('Área do terreno', '', str_replace(str_split('\\://'), "", str_replace("Área total", "", $info))));
                    }
                }
            }
            
            //////////////////////////////////////////////////////////////////
            ///
            ///
            ///
            ///
            ///
            ///
            ///////////////////////////////////////////////////////////////////

            //Inicio da verificação de negociação ////////////////////////////////////////////////
            //
            if($negotiation == 'venda')//Verifica se o imovel é venda
            {
                //Essa váriável armazena o dadso referentes ao valor do imovel
                $priceContents = $xpath->query('//p[@class="price"]');//Query que muda de acordo com a estrutura do site
                
                //Esses loops servem para pegar o valor correto do imóvel
                foreach ($priceContents as $price) 
                {
                    if(!$price == null)
                    {
                        $valor = explode('m²' ,$price->nodeValue);
                        $valorVenda = $valor[1]?? null;
                        $informationToBeStored['valor'] = trim(str_replace('R$', '', $valorVenda));
                        break;
                    }

                }

            }elseif($negotiation == 'locacao')//Verifica se o imovel é locação
            {
                $rentContents = $xpath->query('//p[@class="price"]');//Query que muda de acordo com a estrutura do site
                
                //Esses loops servem para pegar o valor correto do aluguel
                foreach ($rentContents as $rent) 
                {
                    if(!$rent == null)
                    {
                        if(preg_match('/Locação/', $rent->nodeValue))
                        {
                            $valorAluguel = trim(str_replace('/mês', '', str_replace('R$', '', explode('m²' ,$rent->nodeValue)[1])));
                            $informationToBeStored['valor_aluguel'] = trim(str_replace('R$', '', $valorAluguel));
                            break;
                        }
                    }

                }
            }elseif($negotiation == 'venda e locacao')//Verifica se o imovel é venda e locação
            {
                //Essa váriável armazena o dados referentes ao valor do imovel
                $priceContents = $xpath->query('//p[@class="price"]');//Query que muda de acordo com a estrutura do site
                
                //Esses loops servem para pegar o valor correto do imóvel
                foreach ($priceContents as $price) 
                {
                    if(!$price == null)
                    {
                        $valor = explode('m²' ,$price->nodeValue);
                        $valorVenda = $valor[1];
                        $informationToBeStored['valor'] = trim(str_replace('R$', '', $valorVenda));
                        break;
                    }

                }
                
                
                $rentContents = $xpath->query('//p[@class="price"]');//Query que muda de acordo com a estrutura do site
                
                //Esses loops servem para pegar o valor correto do aluguel
                foreach ($rentContents as $rent) 
                {
                    if(!$rent == null)
                    {
                        if(preg_match('/Locação/', $rent->nodeValue))
                        {
                            $valorAluguel = trim(str_replace('/mês', '', str_replace('R$', '', explode('m²' ,$rent->nodeValue)[1]?? null)));
                            $informationToBeStored['valor_aluguel'] = trim(str_replace('R$', '', $valorAluguel));
                            break;
                        }
                    }

                }
            }//Fim da verificação de negociação ////////////////////////////////////////////////


            ////////////////////////////////////////////////////////////////////////
            ///
            ///
            ///
            ///
            ///
            ///
            /////////////////////////////////////////////////////////////////////////            


            //Inicio da verificação por iptu ////////////////////////////////////////////////
            
            $iptuContents = $xpath->query('//p[@class="tax"]');//Query que muda de acordo com a estrutura do site
            
            foreach ($iptuContents as $iptu) 
            {
                ?><pre><?php //var_dump($iptu->nodeValue); ?></pre><?php
                    if(!$iptu == null)
                    {
                        $valorIptu = trim(str_replace('/mês', '', explode('R$' ,$iptu->nodeValue)[1]));

                        $informationToBeStored['iptu'] = $valorIptu;
                        break;
                    }
            }//Fim da verificação por iptu ////////////////////////////////////////////////

            ////////////////////////////////////////////////////////////////////////
            ///
            ///
            ///
            ///
            ///
            ///
            /////////////////////////////////////////////////////////////////////////  

            //Inicio da verificação por titulo e endereco ////////////////////////////////////////////////
            //
            $titleAddrressContents = $xpath->query('//div[@class="header-title hidden-lg-up"]//span');//Query que muda de acordo com a estrutura do site
            //dd($titleContents);
            
            $i = 0;
            foreach ($titleAddrressContents as $titleAddress) 
            {
                if($i == 0)
                {
                    $title = $titleAddress->nodeValue;
                    $informationToBeStored['titulo'] = trim($title);

                }elseif($i == 1) 
                {
                    $address = $titleAddress->nodeValue;
                    $informationToBeStored['endereco'] = trim($address);
                }
                
                $i++;
            }//Fim da verificação por titulo e endereco ////////////////////////////////////////////////

            $titulo_verifier = DB::select('select * from anuncios where title = ?', [
                $informationToBeStored['titulo']
            ]);
            
            if(!$titulo_verifier == 0)
            {
                continue;
                
            }



            ////////////////////////////////////////////////////////////////////////
            ///
            ///
            ///
            ///
            ///
            ///
            /////////////////////////////////////////////////////////////////////////

            //Inicio da verificação por uso ////////////////////////////////////////////////////////////
            
            

            $i = 0;
            foreach ($titleAddrressContents as $titleAddress) 
            {
                if($i == 0)
                {
                    $title = $titleAddress->nodeValue;
                    $title = strtolower(preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"), explode(" ","a A e E i I o O u U n N"), $title));


                    if (preg_match('/edificio/', $title)
                    or 
                    preg_match('/apartamento/', $title) 
                    or 
                    preg_match('/apartamentos/', $title) 
                    or 
                    preg_match('/casa/', $title) 
                    or 
                    preg_match('/casa de condominio/', $title) 
                    or 
                    preg_match('/flat/', $title) 
                    or 
                    preg_match('/cobertura/', $title) 
                    or 
                    preg_match('/kitnet/', $title) 
                    or 
                    preg_match('/edificio residencial/', $title) 
                    or 
                    preg_match('/predio/', $title)  
                    or 
                    preg_match('/fazenda/', $title)
                    or
                    preg_match('/sitio/', $title) 
                    or
                    preg_match('/sobrado/', $title) 
                    or 
                    preg_match('/chacara/', $title)
                    or
                    preg_match('/edificio/', $title2)
                    or 
                    preg_match('/apartamento/', $title2) 
                    or 
                    preg_match('/apartamentos/', $title2) 
                    or 
                    preg_match('/casa/', $title2) 
                    or 
                    preg_match('/casa de condominio/', $title2) 
                    or 
                    preg_match('/flat/', $title2) 
                    or 
                    preg_match('/cobertura/', $title2) 
                    or 
                    preg_match('/kitnet/', $title2) 
                    or 
                    preg_match('/edificio residencial/', $title2) 
                    or 
                    preg_match('/fazenda/', $title2)
                    or
                    preg_match('/sitio/', $title2) 
                    or
                    preg_match('/sobrado/', $title2)
                    or 
                    preg_match('/chacara/', $title2) 
                    or 
                    preg_match('/predio/', $title2))
                    {
                        $informationToBeStored['usage'] = 'residencial';

                    }
                    elseif(preg_match('/consultorio/', $title)
                    or 
                    preg_match('/sala comercial/', $title)
                    or 
                    preg_match('/galpao/', $title)
                    or 
                    preg_match('/deposito/', $title)
                    or 
                    preg_match('/armazen/', $title)
                    or 
                    preg_match('/imovel comercial/', $title)
                    or 
                    preg_match('/loja/', $title)
                    or 
                    preg_match('/ponto comercial/', $title)
                    or
                    preg_match('/consultorio/', $title2)
                    or 
                    preg_match('/sala comercial/', $title2)
                    or 
                    preg_match('/galpao/', $title2)
                    or 
                    preg_match('/deposito/', $title2)
                    or 
                    preg_match('/armazen/', $title2)
                    or 
                    preg_match('/imovel comercial/', $title2)
                    or 
                    preg_match('/loja/', $title2)
                    or 
                    preg_match('/ponto comercial/', $title2))
                    {
                        $informationToBeStored['usage'] = 'comercial';
                    }else
                    {
                        $informationToBeStored['usage'] = 'indefinido/nao_interessa';
                    }
                }//Fim da verificação por uso ///////////////////////////////////////////////////////



                //Inicio da verificação por tipo ////////////////////////////////////////////////////////////
                
                $titleBreadCrumbContents = $xpath->query('//li[@class="item-breadcrumb item-breadcrumb4"]//a');//Query que muda de acordo com a estrutura do site

                if($i == 0)
                {   
                    foreach($titleBreadCrumbContents as $title)
                    {
                        $title2 = $title->nodeValue;
                    }

                    $title2 = strtolower(preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"), explode(" ","a A e E i I o O u U n N"), $title2));


                    $title = $titleAddress->nodeValue;
                    $title = strtolower(preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"), explode(" ","a A e E i I o O u U n N"), $title));


                    if (preg_match('/edificio/', $title) 
                       or 
                       preg_match('/edificio/', $title2))
                    {
                        $informationToBeStored['type'] = 'Edifício Residencial';
                    }
                    
                    if(preg_match('/apartamento/', $title) 
                       or 
                       preg_match('/apartamento/', $title2))
                    {
                        $informationToBeStored['type'] = 'Apartamento';
                    } 
                    
                    if(preg_match('/apartamentos/', $title) 
                       or 
                       preg_match('/predio/', $title) 
                       or 
                       preg_match('/apartamento/', $title2))
                    {
                        $informationToBeStored['type'] = 'Apartamento';
                    } 
                     
                    if(preg_match('/casa/', $title) 
                       or 
                       preg_match('/casa/', $title2))
                    {
                        $informationToBeStored['type'] = 'Casa';
                    } 
                     
                    if(preg_match('/casa de condominio/', $title) 
                       or 
                       preg_match('/casa de condominio/', $title2))
                    {
                        $informationToBeStored['type'] = 'Casa de Condomínio';
                    } 
                     
                    if(preg_match('/flat/', $title) 
                       or 
                       preg_match('/flat/', $title2))
                    {
                        $informationToBeStored['type'] = 'Flat';
                    } 
                 
                    if(preg_match('/cobertura/', $title) 
                       or 
                       preg_match('/cobertura/', $title2))
                    {
                        $informationToBeStored['type'] = 'Cobertura';
                    } 
                     
                    if(preg_match('/kitnet/', $title) 
                       or 
                       preg_match('/kitnet/', $title2))
                    {
                        $informationToBeStored['type'] = 'Kitnet';
                    } 
                     
                    if(preg_match('/edificio residencial/', $title) 
                       or 
                       preg_match('/edificio residencial/', $title2))
                    {
                        $informationToBeStored['type'] = 'Edifício Residencial';
                    } 
                     
                    if(preg_match('/fazenda/', $title) 
                       or 
                       preg_match('/fazenda/', $title2))
                    {
                        $informationToBeStored['type'] = 'Fazenda / Sítio';
                    }
                    
                    if(preg_match('/sitio/', $title) 
                       or 
                       preg_match('/sitio/', $title2))
                    {
                        $informationToBeStored['type'] = 'Fazenda / Sítio';
                    } 
                    
                    if(preg_match('/sobrado/', $title) 
                       or 
                       preg_match('/sobrado/', $title2))
                    {
                        $informationToBeStored['type'] = 'Sobrado';
                    } 
                     
                    if(preg_match('/chacara/', $title) 
                       or 
                       preg_match('/chacara/', $title2))
                    {
                        $informationToBeStored['type'] = 'Chácara';
                    }

                    if(preg_match('/consultorio/', $title) 
                       or 
                       preg_match('/consultorio/', $title2))
                    {
                        $informationToBeStored['type'] = 'Consultório';
                    }

                    if(preg_match('/sala comercial/', $title) 
                       or 
                       preg_match('/sala comercial/', $title2))
                    {
                        $informationToBeStored['type'] = 'Sala Comercial';
                    }

                    if(preg_match('/galpao/', $title) 
                       or 
                       preg_match('/galpao/', $title2))
                    {
                        $informationToBeStored['type'] = 'Galpão / Depósito / Armazém';
                    }

                    if(preg_match('/deposito/', $title) 
                       or 
                       preg_match('/deposito/', $title2))
                    {
                        $informationToBeStored['type'] = 'Galpão / Depósito / Armazém';
                    }

                    if(preg_match('/armazen/', $title) 
                       or 
                       preg_match('/armazen/', $title2))
                    {
                        $informationToBeStored['type'] = 'Galpão / Depósito / Armazém';
                    }

                    if(preg_match('/imovel comercial/', $title) 
                       or 
                       preg_match('/imovel comercial/', $title2))
                    {
                        $informationToBeStored['type'] = 'Imóvel Comercial';
                    }

                    if(preg_match('/loja/', $title) 
                       or 
                       preg_match('/loja/', $title2))
                    {
                        $informationToBeStored['type'] = 'Loja';
                    }

                    if(preg_match('/ponto comercial/', $title) 
                       or 
                       preg_match('/ponto comercial/', $title2))
                    {
                        $informationToBeStored['type'] = 'Ponto Comercial';
                    }

                }//Fim da verificação por uso ////////////////////////////////////////////////////////////

                //dd($informationToBeStored);

                $i++;
            }
            
            if(preg_match('/edificio/', $title) or preg_match('/edificio/', $title2) or preg_match('/terreno/', $title) or preg_match('/terreno/', $title2))
            {
                continue;
            }

            ////////////////////////////////////////////////////////////////////////
            ///
            ///
            ///
            ///
            ///
            ///
            /////////////////////////////////////////////////////////////////////////  

            //Inicio da verificação por descrição ////////////////////////////////////////////////
            
            $descriptionContents = $xpath->query('//div[@class="box-description"]');//Query que muda de acordo com a estrutura do site
            

            foreach ($descriptionContents as $description)
            {
                $informationToBeStored['descricao'] = trim($description->nodeValue);
            }//Fim da verificação por descrição ////////////////////////////////////////////////

            ////////////////////////////////////////////////////////////////////////
            ///
            ///
            ///
            ///
            ///
            ///
            /////////////////////////////////////////////////////////////////////////  

            //Inicio da verificação pelo link das imagens ////////////////////////////////////////////////
            

            $endereco = explode(' - ', $informationToBeStored['endereco']);


            /*if(empty($informationToBeStored['area_util']) 
                and
                empty($informationToBeStored['area_total']) 
                or 
                empty($informationToBeStored['valor']) 
                or
                empty($informationToBeStored['titulo'])
                or 
                empty($endereco[1]))
            {
                continue;

            }*/
            
            $site = file_get_contents($right_link);

            $array = array();
            $start = '<div class="slider-wrap">';
            $end = '<div style="width:100%;min-height:50px;display:initial;" class="hidden-lg-up">';

            $parsed = $this->service->getStringBetween($site, $start, $end);


            $links = explode('src="', $parsed);

            $i = 0;
            foreach ($links as $link_parsed)
            {
                if
                (
                    preg_match('~https://imgs.kenlo.io/~', $link_parsed)
                )
                {
                    $data_broken = $this->service->getStringBetween($link_parsed, 'https://imgs.kenlo.io/', '.jpg"');
                    $data[$i] = 'https://imgs.kenlo.io/'.$data_broken.'.jpg';

                    $i++;
                }
            }

            $informationToBeStored['images'] = $data;

            //Fim da verificação pelo link das imagens ////////////////////////////////////////////////

            ////////////////////////////////////////////////////////////////////////
            ///
            ///
            ///
            ///
            ///
            ///
            /////////////////////////////////////////////////////////////////////////  

            ////Inicio do dowload das imagens /////////////////////////////////////////////////////////
            $i = 0;
            foreach ($informationToBeStored['images'] as $linkToBeDowloaded) 
            {
                $ch = curl_init($linkToBeDowloaded);

                $fp = fopen('images/'.str_replace(str_split('/\ :'), '', Carbon::now()).'-'.sha1($linkToBeDowloaded).'.jpg', 'wb') or die("fail to open file");

                $informationToBeStored['images'][$i] = str_replace(str_split('/\ :'), '', Carbon::now()).'-'.sha1($linkToBeDowloaded).'.jpg';

                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt( $ch, CURLOPT_URL, $linkToBeDowloaded);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );
                curl_exec($ch);

                file_put_contents('images/'.str_replace(str_split('/\ :'), '', Carbon::now()).'-'.sha1($linkToBeDowloaded).'.jpg', file_get_contents($linkToBeDowloaded));
                
                curl_close($ch);
                fclose($fp);
                $i++;

            }////Fim do dowload das imagens /////////////////////////////////////////////////////////

            ////////////////////////////////////////////////////////////////////////
            ///
            ///
            ///
            ///
            ///
            ///
            /////////////////////////////////////////////////////////////////////////

            $dados_guardados = $this->service->storeData($informationToBeStored, $link->id);

            echo 'loop finalizado<br/>';/**/
        }
        
    }
}
