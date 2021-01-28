<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->longText("description");
            $table->text("excerpt");
            $table->string("url");
            $table->longText("image")->nullable();
            $table->bigInteger("user_id")->unsigned();
            $table->tinyInteger("allow_comments")->default("0");
            $table->tinyInteger("status")->default("0");
            $table->bigInteger("format_id")->unsigned();
            $table->timestamps();
        });
        Schema::table('posts',function (Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
