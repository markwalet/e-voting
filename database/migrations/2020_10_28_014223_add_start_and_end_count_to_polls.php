<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartAndEndCountToPolls extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('polls', static function (Blueprint $table) {
            $table->unsignedSmallInteger('start_count')->nullable()->default(null);
            $table->unsignedSmallInteger('end_count')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('polls', static function (Blueprint $table) {
            $table->dropColumn('start_count');
            $table->dropColumn('end_count');
        });
    }
}
