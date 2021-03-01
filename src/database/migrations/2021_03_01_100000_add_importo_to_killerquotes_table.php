<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImportoToKillerquotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('killer_quotes', function (Blueprint $table) {
            if(!Schema::hasColumn('killer_quotes','importo'))
            {
                $table->decimal('importo', 8,2)->default(0);
                $table->string('filename')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('killer_quotes', function (Blueprint $table) {
            //
        });
    }
}
