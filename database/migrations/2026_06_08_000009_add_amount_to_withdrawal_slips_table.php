<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawal_slips', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->default(0.00)->after('requested_items');
        });
    }

    public function down(): void
    {
        Schema::table('withdrawal_slips', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
