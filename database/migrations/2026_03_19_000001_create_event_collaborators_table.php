<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('inviter_user_id')->constrained('users')->onDelete('cascade');
            $table->string('invitee_email');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'active', 'revoked'])->default('pending');
            $table->dateTime('expires_at');
            $table->dateTime('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_collaborators');
    }
};
