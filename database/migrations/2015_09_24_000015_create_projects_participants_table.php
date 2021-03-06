<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateProjectsParticipantsTable
 *
 * Generated by ViKon\DbExporter
 */
class CreateProjectsParticipantsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('projects_participants', function(Blueprint $table) {
            $table->increments('id')
                  ->unsigned();
            $table->integer('participant_id')
                  ->unsigned();
            $table->integer('project_id')
                  ->unsigned();
            $table->timestamps();
            $table->index('participant_id', 'projects_participants_participant_id_foreign');
            $table->index('project_id', 'projects_participants_project_id_foreign');
            $table->foreign('project_id', 'projects_participants_project_id_foreign')
                  ->references('id')
                  ->on('projects');

            $table->foreign('participant_id', 'projects_participants_participant_id_foreign')
                  ->references('id')
                  ->on('participants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('projects_participants', function(Blueprint $table) {
            $table->dropForeign('projects_participants_project_id_foreign');
            $table->dropForeign('projects_participants_participant_id_foreign');
        });
        Schema::dropIfExists('projects_participants');
    }
}