<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('pcode', function(Blueprint $table){
            
            $table->foreign('org_id')
                  ->references('id')
                  ->on('organizations')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
        
        Schema::table('participants', function(Blueprint $table){
            $table->foreign('role_id')
                  ->references('id')
                  ->on('participant_roles');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pcode', function(Blueprint $table){
            $table->dropForeign('pcode_org_id_foreign');
            $table->dropForeign('pcode_pid_foreign');
            $table->dropForeign('pcode_lid_foreign');
        });
        Schema::table('participants', function(Blueprint $table){
            $table->dropForeign('participants_role_id_foreign');
        }); 
        
    }
}
