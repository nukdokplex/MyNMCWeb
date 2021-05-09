<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentaryHasCommentariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commentary_has_commentaries', function (Blueprint $table) {
            $table->unsignedBigInteger('commentary_id');
            $table->unsignedBigInteger('reply_id')->index('FK_commentary_has_commentaries_reply_id');
            $table->primary(['commentary_id', 'reply_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commentary_has_commentaries');
    }
}
