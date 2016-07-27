<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qanswers', function(Blueprint $table){
            $table->increments('id');
            $table->integer('qid')->unsigned();
            $table->string('type')->index();
            $table->string('text')->index();
            $table->string('value')->index();
            $table->string('akey')->index();
            $table->string('qarequire');
            $table->string('css');
            $table->text('logic');
            $table->string('optional');
            $table->timestamps();
            $table->unique(['qid','akey']);
            $table->foreign('qid')
                  ->references('id')
                  ->on('questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qanswers', function(Blueprint $table){
            $table->dropForeign('qanswers_qid_foreign');
        });
        Schema::dropIfExists('qanswers');
    }
}
