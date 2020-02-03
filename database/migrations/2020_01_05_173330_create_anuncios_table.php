<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnunciosTable extends Migration
{
    public function up()
    {
        Schema::create('anuncios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('link_id')
                ->unsigned()
                ->index('anuncios_link_id_foreign');
            $table->integer('imobiliaria_id')
                ->unsigned()
                ->index('anuncios_imobiliaria_id_foreign');
            $table->string('suites')->nullable();
            $table->string('bedrooms')->default(0)->nullable();
            $table->string('bathrooms')->default(0)->nullable();
            $table->string('area_util')->nullable();
            $table->string('area_total')->nullable();
            $table->string('parking')->default(0)->nullable();
            $table->string('title')->nullable();
            $table->text('description', 65535)->nullable();
            $table->string('valor')->nullable();
            $table->string('valor_aluguel')->nullable()->nullable();
            $table->string('iptu')->nullable();
            $table->string('endereco')->nullable();
            $table->string('usage')->nullable();
            $table->string('type')->nullable();
            $table->string('negotiation')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('anuncios');
    }
}
