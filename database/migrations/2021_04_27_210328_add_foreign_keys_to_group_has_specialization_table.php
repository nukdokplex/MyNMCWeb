<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToGroupHasSpecializationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_has_specialization', function (Blueprint $table) {
            $table->foreign('group_id', 'FK_group_has_specialization_group_id')->references('id')->on('groups')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('specialization_id', 'FK_group_has_specialization_specialization_id')->references('id')->on('specializations')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_has_specialization', function (Blueprint $table) {
            $table->dropForeign('FK_group_has_specialization_group_id');
            $table->dropForeign('FK_group_has_specialization_specialization_id');
        });
    }
}
