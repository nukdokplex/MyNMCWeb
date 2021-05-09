<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToModelHasFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('model_has_file', function (Blueprint $table) {
            $table->foreign('file_id', 'FK_model_has_file_file_id')->references('id')->on('files')->onUpdate('CASCADE')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('model_has_file', function (Blueprint $table) {
            $table->dropForeign('FK_model_has_file_file_id');
        });
    }
}
