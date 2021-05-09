<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommentariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commentaries', function (Blueprint $table) {
            $table->foreign('author_id', 'FK_commentaries_author_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('NO ACTION');
            $table->foreign('post_id', 'FK_commentaries_post_id')->references('id')->on('posts')->onUpdate('CASCADE')->onDelete('NO ACTION');
            $table->foreign('updated_by', 'FK_commentaries_updated_by')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commentaries', function (Blueprint $table) {
            $table->dropForeign('FK_commentaries_author_id');
            $table->dropForeign('FK_commentaries_post_id');
            $table->dropForeign('FK_commentaries_updated_by');
        });
    }
}
