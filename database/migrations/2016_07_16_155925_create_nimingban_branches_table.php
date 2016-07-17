<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNimingbanBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nimingban_branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('section');
            $table->string('authorId');
            $table->string('authorName', 16)->nullable();
            $table->string('title', 32)->nullable();
            $table->string('content', 2000);
            $table->timestamps();

            $table->index('section');
            $table->index('authorId');
            $table->index('authorName');
            $table->index('title');
            $table->index('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nimingban_branches');
    }
}
