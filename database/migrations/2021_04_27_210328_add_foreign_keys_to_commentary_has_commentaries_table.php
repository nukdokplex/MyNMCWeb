<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommentaryHasCommentariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commentary_has_commentaries', function (Blueprint $table) {
            $table->foreign('commentary_id', 'FK_commentary_has_commentaries_commentary_id')->references('id')->on('commentaries')->onUpdate('CASCADE')->onDelete('NO ACTION');
            $table->foreign('reply_id', 'FK_commentary_has_commentaries_reply_id')->references('id')->on('commentaries')->onUpdate('CASCADE')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commentary_has_commentaries', function (Blueprint $table) {
            $table->dropForeign('FK_commentary_has_commentaries_commentary_id');
            $table->dropForeign('FK_commentary_has_commentaries_reply_id');
        });
    }
}
