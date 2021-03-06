<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('introduction')->default("please input introduction");
            $table->integer('size');
            $table->string("type");
            $table->integer("width");
            $table->integer("height");
            $table->string('uri');
            $table->boolean("has_uploaded_to_cdn")->default(false);
            $table->boolean("has_thumbnail")->default(false);
            $table->boolean("thumbnail_has_uploaded_to_cdn")->default(false);
            $table->string('thumbnail_uri');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
};
