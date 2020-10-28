<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompletedAtToPollsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('polls', static function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->default(null)->after('ended_at');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('polls', static function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
}
