<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKillerQuoteSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('killer_quote_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->index()->unique();
            $table->longText('value');
            $table->dateTime('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('killer_quote_settings');
    }
}
