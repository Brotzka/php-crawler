<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('urls', function(Blueprint $table){
            $table->boolean('robots_index')->default(false);
            $table->boolean('robots_follow')->default(false);
            $table->smallInteger('status_code')->unsigned()->nullable()->default(NULL);
            $table->string('reason_phrase')->nullable()->default(NULL);
            $table->string('location')->nullable()->default(NULL);
            $table->string('content_type')->nullable()->default(NULL);
            $table->integer('content_length')->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('urls', function(Blueprint $table){
            $table->dropColumn([
                'robots_index',
                'robots_follow',
                'status_code',
                'reason_phrase',
                'location',
                'content_type',
                'content_length'
            ]);
        });
    }
}
