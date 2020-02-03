Alexia Web-Crowler

O Alexia é um robô capaz de extrair dados do site da Imobiliária Figueiras.

///////////////////////////////////

Seu funcionamento consiste nas seguintes etapas:

- Alx-A Robot: Pegar a Url de paginação dos anuncios(http://www.imobiliariafigueira.com.br/imoveis/a-venda?pagina=1) filtrar todos aqueles links referentes aos anuncios e salválos em uma tabela do banco de dados;

Para usar esse robô(Alx-A) basta se registrar na aplicação ou se logar, dai você será redirecionado para a home onde não precisa digitar nada apenas clicar em "Processar". Feito isso ele automaticamente irá salvar os dados no BD.

Utilizei uma camada de abstração chamada Services, de maneira que os controller preparam os dados e o Service aramazena;

- Alx-B Robot: Ir em cada Url salva no banco de dados e extrair os dados dela, ainda seguindo a ideia de camada de abstração, utilizo os Services para armazenar enquanto o Controller prepara os dados, porém  devo admitr que devido algumas alterações que fiz no decorrer do desenvolvimento estes ficaram um pouco fora de seu escopo e alguns dados são tratados dentro do próprio Service.

Para usar esse robô é necessário que tenha realizado a primeira etapa(Alx-A), depois basta entrar no link http://"localhost:8000/axl-b";

///////////////////////////////////

Informações extras:

A ideia é fazer com que esse robô seja capaz de pegar os dados de qualquer site imobiliário, por isso comecei a criar um layout com inputs para colocar a informação necessária para realizar a extração de dados;

