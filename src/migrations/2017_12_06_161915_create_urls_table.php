<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned()->default(0);
            $table->string('full_url');
            
            // Ergebnisse aus parse_url()
            $table->string('scheme')->nullable()->default(NULL);
            $table->string('host')->nullable()->default(NULL);
            $table->string('user')->nullable()->default(NULL);
            $table->string('pass')->nullable()->default(NULL);
            $table->smallInteger('port')->unsigned()->nullable()->default(NULL);
            $table->string('path')->nullable()->default(NULL);
            $table->string('query')->nullable()->default(NULL);
            $table->string('fragment')->nullable()->default(NULL);
            
            $table->timestamps();
            $table->unique('full_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('urls');
    }
}
