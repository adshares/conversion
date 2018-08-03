<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GenesisFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genesis_nodes', function (Blueprint $table) {
            $table->string('id');
            $table->integer('num');
            $table->string('public_key');
            $table->string('type');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unique('id', '_id');
        });

        Schema::create('genesis_accounts', function (Blueprint $table) {
            $table->string('node_id');
            $table->string('id');
            $table->integer('num');
            $table->string('address');
            $table->float('amount', 16, 4);
            $table->string('public_key');
            $table->string('type');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unique(['node_id','id'], '_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('genesis_nodes');
        Schema::drop('genesis_accounts');
    }
}
