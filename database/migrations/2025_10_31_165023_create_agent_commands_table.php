<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentCommandsTable extends Migration
{
    public function up()
    {
        Schema::create('agent_commands', function (Blueprint $table) {
            $table->id();
            $table->string('target_device')->nullable();
            $table->string('command');          // small command name like 'screenshot'
            $table->text('args')->nullable();   // JSON-encoded args
            $table->string('status')->default('pending'); // pending, running, done, failed
            $table->text('result')->nullable(); // result or encoded data
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_commands');
    }
}
