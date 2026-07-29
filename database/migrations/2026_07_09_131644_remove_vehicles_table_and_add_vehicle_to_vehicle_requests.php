<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop vehicle_id from trip_tickets
        Schema::table('trip_tickets', function (Blueprint $table) {
            try {
                $table->dropForeign(['vehicle_id']);
            } catch (\Exception $e) {}
            $table->dropColumn('vehicle_id');
            $table->string('vehicle')->nullable()->after('driver_id');
        });

        // 2. Drop vehicle_id from vehicle_requests
        Schema::table('vehicle_requests', function (Blueprint $table) {
            try {
                $table->dropForeign(['vehicle_id']);
            } catch (\Exception $e) {}
            $table->dropColumn('vehicle_id');
            $table->string('vehicle')->nullable()->after('employee_name');
        });

        // 3. Drop vehicles table
        Schema::dropIfExists('vehicles');
    }

    public function down(): void
    {
        // No need to implement rollback for this cleanup migration
    }
};
