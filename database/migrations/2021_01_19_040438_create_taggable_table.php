<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaggableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taggable', function (Blueprint $table) {
            $table->bigInteger("taggable_id");
            $table->string("taggable_type");
            $table->bigInteger("tag_id")->unsigned();
            $table->timestamps();
        });
        Schema::table('taggable', function (Blueprint $table) {
            $table->primary(['taggable_id', 'tag_id',"taggable_type"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taggable');
    }
}
