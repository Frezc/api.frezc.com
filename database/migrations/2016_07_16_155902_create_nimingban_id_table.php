<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNimingbanIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nimingban_id', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uniqueId');
            $table->timestamps();

            $table->index('uniqueId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nimingban_id');
    }
}
