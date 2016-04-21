<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreatePcodeTable
 * Custom Location Code table
 * Pivot table for participants, locations, organization
 */
class CreatePcodeTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::create('pcode', function(Blueprint $table) 
        {
            $table->string('primaryid')->unique()->primary();
            $table->integer('org_id')
                    ->unsigned();
            $table->string('pcode');
            $table->string('ueccode');
            $table->text('village');
            $table->text('village_tract');
            $table->text('township');
            $table->text('district');
            $table->text('state');
            $table->text('country');
            $table->string('isocode')->nullable();
            $table->float('lon')
                  ->nullable()
                  ->default(NULL);
            $table->float('lat')
                  ->nullable()
                  ->default(NULL);            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
       // Schema::table('participants', function(Blueprint $table){
       //     $table->dropForeign('participants_pcode_id_foreign');
       // });
        Schema::dropIfExists('pcode');
    }
}