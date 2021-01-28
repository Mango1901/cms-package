<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateAbleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cateable', function (Blueprint $table) {
            $table->bigInteger("cateable_id");
            $table->String("cateable_type");
            $table->bigInteger("category_id")->unsigned();
            $table->timestamps();
        });
        Schema::table('cateable', function (Blueprint $table) {
            $table->primary(['cateable_id', 'category_id',"cateable_type"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cateable');
    }
}
