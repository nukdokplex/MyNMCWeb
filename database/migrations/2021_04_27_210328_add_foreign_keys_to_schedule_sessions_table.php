<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToScheduleSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_sessions', function (Blueprint $table) {
            $table->foreign('auditory_id', 'FK_schedule_sessions_auditory_id')->references('id')->on('auditories')->onUpdate('CASCADE')->onDelete('NO ACTION');
            $table->foreign('group_id', 'FK_schedule_sessions_group_id')->references('id')->on('groups')->onUpdate('CASCADE')->onDelete('NO ACTION');
            $table->foreign('subject_id', 'FK_schedule_sessions_subject_id')->references('id')->on('subjects')->onUpdate('CASCADE')->onDelete('NO ACTION');
            $table->foreign('teacher_id', 'FK_schedule_sessions_teacher_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_sessions', function (Blueprint $table) {
            $table->dropForeign('FK_schedule_sessions_auditory_id');
            $table->dropForeign('FK_schedule_sessions_group_id');
            $table->dropForeign('FK_schedule_sessions_subject_id');
            $table->dropForeign('FK_schedule_sessions_teacher_id');
        });
    }
}
