<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateAssignedRolesTable
 *
 * Generated by ViKon\DbExporter
 */
class CreateAssignedRolesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('assigned_roles', function(Blueprint $table) {
            $table->increments('id')
                  ->unsigned();
            $table->integer('user_id')
                  ->unsigned();
            $table->integer('role_id')
                  ->unsigned();
            $table->index('user_id', 'assigned_roles_user_id_foreign');
            $table->index('role_id', 'assigned_roles_role_id_foreign');
            $table->foreign('role_id', 'assigned_roles_role_id_foreign')
                  ->references('id')
                  ->on('roles');

            $table->foreign('user_id', 'assigned_roles_user_id_foreign')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('CASCADE')
                  ->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('assigned_roles');
    }
}