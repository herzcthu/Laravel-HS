<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateParticipantsTable
 *
 * Generated by ViKon\DbExporter
 */
class CreateParticipantsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('participants', function(Blueprint $table) {
            $table->increments('id')
                  ->unsigned();
            $table->integer('parent_id')
                  ->nullable()
                  ->default(NULL);
            $table->string('avatar', 255)->nullable();
            $table->string('name', 255)->nullable()->index();
            $table->string('participant_code', 255)->nullable()->index();
            $table->string('nrc_id', 255)->nullable()->unique();
            $table->string('email', 255)
                  ->nullable()
                  ->index();
            $table->string('race', 255)
                  ->nullable()
                  ->index();
            $table->string('education', 255)
                  ->nullable()
                  ->index();
            $table->text('bank_info');
            $table->string('occupation', 255)
                  ->nullable()
                  ->index();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable()->default('Unspecified');
            $table->json('phones')->nullable();
            $table->text('address');
            $table->integer('org_id')->unsigned()->nullable();
            $table->json('locations');           
            $table->integer('role_id')->unsigned()->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('participants');
    }
}