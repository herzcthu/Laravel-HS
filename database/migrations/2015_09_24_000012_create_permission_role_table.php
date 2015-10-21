<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreatePermissionRoleTable
 *
 * Generated by ViKon\DbExporter
 */
class CreatePermissionRoleTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('permission_role', function(Blueprint $table) {
            $table->increments('id')
                  ->unsigned();
            $table->integer('permission_id')
                  ->unsigned();
            $table->integer('role_id')
                  ->unsigned();
            $table->index('permission_id', 'permission_role_permission_id_foreign');
            $table->index('role_id', 'permission_role_role_id_foreign');
            $table->foreign('role_id', 'permission_role_role_id_foreign')
                  ->references('id')
                  ->on('roles');

            $table->foreign('permission_id', 'permission_role_permission_id_foreign')
                  ->references('id')
                  ->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('permission_role');
    }
}