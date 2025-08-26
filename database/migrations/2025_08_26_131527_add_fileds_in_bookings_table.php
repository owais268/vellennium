<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dateTime('start_time')->change();
            $table->dateTime('end_time')->change();

            // Add new columns
            $table->unsignedBigInteger('customer_id')->after('partner_id');
            $table->unsignedBigInteger('service_id')->after('customer_id');
            $table->enum('status', [
                'pending',
                'confirmed',
                'in_progress',
                'completed',
                'canceled'
            ])->default('pending')->after('end_time');
            $table->string('verification_code', 6)->nullable()->after('status');
            $table->decimal('price', 10, 2)->after('verification_code');

            $table->index('customer_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
