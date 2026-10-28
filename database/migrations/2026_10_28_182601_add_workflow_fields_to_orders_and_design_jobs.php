<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable()->after('work_status');
            $table->string('cancellation_type', 20)->nullable()->after('cancellation_reason');
            $table->timestamp('completed_at')->nullable()->after('cancellation_type');
        });

        Schema::table('design_jobs', function (Blueprint $table) {
            $table->text('revision_notes')->nullable()->after('brief_notes');
            $table->unsignedInteger('revision_count')->default(0)->after('revision_notes');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cash_movements MODIFY COLUMN category ENUM('order_dp', 'order_settlement', 'retail_pos', 'petty_cash_expense', 'initial_balance', 'order_refund') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancellation_reason', 'cancellation_type', 'completed_at']);
        });

        Schema::table('design_jobs', function (Blueprint $table) {
            $table->dropColumn(['revision_notes', 'revision_count']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cash_movements MODIFY COLUMN category ENUM('order_dp', 'order_settlement', 'retail_pos', 'petty_cash_expense', 'initial_balance') NOT NULL");
        }
    }
};
