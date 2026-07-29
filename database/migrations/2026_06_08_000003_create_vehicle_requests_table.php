<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->string('employee_name');
            $table->string('destination');
            $table->date('date');
            $table->time('time');
            $table->string('status')->default('pending'); // pending, approved, rejected, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_requests');
    }
};
