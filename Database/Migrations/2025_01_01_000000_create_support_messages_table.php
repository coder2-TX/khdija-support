<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->enum('sender_role', ['business','admin']);
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('context_user_id')->nullable();
            $table->text('body');
            $table->timestamp('read_by_admin_at')->nullable();
            $table->timestamp('read_by_business_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'created_at']);
            $table->index(['business_id', 'context_user_id']);
            $table->index(['sender_role', 'read_by_admin_at']);
            $table->index(['sender_role', 'read_by_business_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_messages');
    }
};