<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKillerQuoteNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('killer_quote_notes', function (Blueprint $table) {
             $table->increments('id');
             $table->integer('killer_quote_id')->unsigned();
             $table->foreign('killer_quote_id')->references('id')->on('killer_quotes');
             $table->text('note')->nullable();
             $table->timestamps();
         });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::dropIfExists('killer_quote_notes');
     }
}
