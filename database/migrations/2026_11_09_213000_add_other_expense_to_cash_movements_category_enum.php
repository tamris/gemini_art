<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cash_movements MODIFY COLUMN category ENUM('order_dp', 'order_settlement', 'retail_pos', 'petty_cash_expense', 'initial_balance', 'order_refund', 'other_expense') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cash_movements MODIFY COLUMN category ENUM('order_dp', 'order_settlement', 'retail_pos', 'petty_cash_expense', 'initial_balance', 'order_refund') NOT NULL");
        }
    }
};

