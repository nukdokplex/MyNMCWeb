<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelHasFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_has_file', function (Blueprint $table) {
            $table->unsignedBigInteger('model_id');
            $table->string('model_type')->default('');
            $table->unsignedBigInteger('file_id')->index('FK_model_has_file_file_id');
            $table->primary(['model_id', 'model_type', 'file_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_has_file');
    }
}
