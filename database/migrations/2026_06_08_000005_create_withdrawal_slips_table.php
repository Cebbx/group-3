<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_slips', function (Blueprint $table) {
            $table->id();
            $table->string('slip_number')->unique();
            $table->foreignId('trip_ticket_id')->constrained('trip_tickets')->onDelete('cascade');
            $table->string('purpose');
            $table->text('requested_items');
            $table->string('status')->default('pending'); // pending, approved, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_slips');
    }
};
