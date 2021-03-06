<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('title', 30);
            // change!: 'todo': 未开始或正在进行中, 'complete': 完成, 'layside': 搁置, 'abandon': 放弃
            $table->string('status', 16)->default('todo');
            // 类型
            $table->string('type')->default('default');
            // 为了直接保存为时间戳
            $table->integer('start_at')->unsigned()->default(0);
            // 预警时间
            $table->integer('urgent_at')->unsigned()->default(0);
            // 预计的期限
            $table->integer('deadline')->unsigned()->default(0);
            // 优先级， [1, 9]， 默认5
            $table->tinyInteger('priority')->default(5);
            // 地点
            $table->string('location')->default('');
            // 对应status 0：null，1：完成时间，2：搁置时间，3：放弃时间
            $table->integer('end_at')->unsigned()->nullable();

            $table->text('contents');


            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('todos');
    }
}
