<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Links extends Migration
{

    public function up()
    {
        Schema::create('links', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->integer('imobiliaria_id')
            ->unsigned()
            ->index('links_imobiliaria_id_foreign');
            $table->string('link');
            $table->string('negotiation');
            $table->timestamps();
        });
        
    }


    public function down()
    {
        Schema::drop('links');
    }
}
