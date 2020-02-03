@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Inicio do form das URLs -->
                    <div class="align-content-center">
                        <form class="form-check" method="post" action="{!! route('alx-a.find') !!}">
                            @csrf
                            <h2 class="card-title">Identificador de URLs</h2>

                            <p>Esse campos serão necessários apenas em atualizações futuras do Alexia Robot, portanto não é necessário preenchelos com qualquer tipo de informação.</p>

                            <input type="text" class="input-group m-2 border" name="nNegotiation" placeholder="Digite aqui se o link lista compra ou venda" required value="Basta clicar em 'Processar'">
                            <input type="text" class="input-group m-2 border" name="nName" placeholder="Digite aqui o nome da Imobiliaria" required value="Basta clicar em 'Processar'">
                            <input type="text" class="input-group m-2 border" name="nUrlIni" placeholder="Digite aqui a sua URL inicial" required value="Basta clicar em 'Processar'">
                            <input type="text" class="input-group m-2 border input-group" name="nUrlFim" placeholder="Digite aqui a sua URL final" required value="Basta clicar em 'Processar'">
                            <input type="text" class="input-group m-2 border input-group" name="nUrlDelimiter" placeholder="Digite aqui o delimitador de paginação da Url" required value="Basta clicar em 'Processar'">
                            <!-- input type="text" class="input-group m-2 border input-group" name="nPrefixo1" placeholder="Digite aqui o prefixo padrão de cada anuncio" required -->
                            <!-- input type="text" class="input-group m-2 border input-group" name="nPrefixo2" placeholder="Digite aqui o segundo prefixo padrão de cada anuncio" -->
                            <div class="text-center">
                                <input type="submit" class="btn btn-primary" value="Processar">
                            </div>
                        </form>
                    </div>
                    <!-- Fim do form das URLs -->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
