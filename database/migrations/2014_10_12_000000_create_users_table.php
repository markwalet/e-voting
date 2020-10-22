<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('conscribo_id')->unique();

            // Name, email and phone
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable()->default(null);

            // Verification
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Roles
            $table->boolean('is_voter')->default(0);
            $table->boolean('is_admin')->default(0);
            $table->boolean('is_monitor')->default(0);

            // Shared secret
            $table->text('totp_secret');

            // Remember token and timestamps
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
