<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateResultsTable
 *
 * Generated by ViKon\DbExporter
 */
class CreateResultsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('results', function(Blueprint $table) {
            $table->increments('id')
                  ->unsigned();
            $table->json('results');
            $table->string('information')->index();
            //$table->integer('participant_id')->unsigned()->index();
            //$table->string('station_id')->index();
            $table->integer('project_id')->index()
                 ->unsigned();
            $table->integer('user_id')->index()
                  ->unsigned();            
            $table->integer('section_id')->index();
            $table->integer('incident_id')->nullable()->index();
            //$table->morphs('resultable');
            $table->string('resultable_id');
            $table->string('resultable_type');
            $table->timestamps();
            $table->softDeletes();
            
           //$table->foreign('participant_id')
           //         ->references('id')
            //        ->on('participants');
            
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects');

            //$table->foreign('station_id')
            //      ->references('primaryid')
            //      ->on('pcode');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('results', function(Blueprint $table) {
            //$table->dropForeign('results_participant_id_foreign');
            $table->dropForeign('results_project_id_foreign');
            //$table->dropForeign('results_station_id_foreign');
            $table->dropForeign('results_user_id_foreign');
        });
        Schema::dropIfExists('results');
    }
}