<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanProxyToUsersTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->boolean('can_proxy')->default(0)->after('is_present');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn('can_proxy');
        });
    }
}
