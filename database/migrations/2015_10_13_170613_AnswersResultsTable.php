<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AnswersResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function(Blueprint $table){
            $table->increments('id');
            $table->integer('qid')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->text('value');
            $table->string('akey')->index();
            $table->foreign('qid')
                  ->references('id')
                  ->on('questions');
            
            $table->foreign('status_id')
                  ->references('id')
                  ->on('results');
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
        Schema::table('answers', function(Blueprint $table){
            $table->dropForeign('answers_qid_foreign');
            $table->dropForeign('answers_status_id_foreign');
        });
        Schema::dropIfExists('answers');
    }
}
