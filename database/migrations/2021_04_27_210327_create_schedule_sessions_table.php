<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('group_id')->index('FK_schedule_sessions_group_id');
            $table->unsignedBigInteger('teacher_id')->index('FK_schedule_sessions_teacher_id');
            $table->unsignedBigInteger('subject_id')->index('FK_schedule_sessions_subject_id');
            $table->unsignedBigInteger('auditory_id')->index('FK_schedule_sessions_auditory_id');
            $table->unsignedInteger('number');
            $table->unsignedInteger('subgroup')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('interrupts_at')->nullable();
            $table->timestamp('continues_at')->nullable();
            $table->timestamp('ends_at')->nullable();
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
        Schema::dropIfExists('schedule_sessions');
    }
}
