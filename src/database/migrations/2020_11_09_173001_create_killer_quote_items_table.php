<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKillerQuoteItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('killer_quote_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned()->index();
            $table->foreign('product_id')->references('id')->on('products');
            $table->text('descrizione')->nullable();
            $table->decimal('qta', 7, 2)->nullable();
            $table->float('importo', 9, 4)->nullable();
            $table->float('sconto', 7, 4)->default(0);
            $table->string('perc_iva')->nullable();
            $table->float('iva', 9, 4)->nullable();
            $table->integer('invoice_id')->unsigned()->index();
            $table->foreign('invoice_id')->references('id')->on('killer_quotes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('killer_quote_items');
    }
}
