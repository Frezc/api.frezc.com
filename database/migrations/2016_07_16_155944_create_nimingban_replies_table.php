<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNimingbanRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nimingban_replies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('branchId');
            $table->string('authorId');
            $table->string('authorName', 16)->nullable();
            $table->string('content', 2000);
            $table->integer('floor')->unsigned();
            $table->integer('replyToId')->unsigned()->default(0);
            $table->integer('replyToFloor')->unsigned()->default(0);

            $table->timestamps();

            $table->index('branchId');
            $table->index('authorId');
            $table->index('authorName');
            $table->index('content');
            $table->index('replyToId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nimingban_replies');
    }
}
