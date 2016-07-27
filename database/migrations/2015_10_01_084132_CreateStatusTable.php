<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
        Schema::create('status', function(Blueprint $table){
            $table->increments('id');
            $table->string('station_id')->index();
            $table->integer('project_id')->unsigned();
            $table->json('status');
            $table->timestamps();
            
            $table->foreign('station_id')
                  ->references('primaryid')
                  ->on('pcode');
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects');
        });
         * 
         */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::table('status', function(Blueprint $table){
        //    $table->dropForeign('status_station_id_foreign');
        //    $table->dropForeign('status_project_id_foreign');
        //});
        //Schema::drop('status');
    }
}
