<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('poll_approvals', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('poll_id')->constrained();
            $table->foreignId('user_id')->constrained();

            $table->string('result');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_approvals');
    }
}
