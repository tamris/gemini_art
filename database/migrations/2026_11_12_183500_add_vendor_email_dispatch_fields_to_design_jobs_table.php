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
        Schema::table('design_jobs', function (Blueprint $table) {
            $table->timestamp('vendor_email_sent_at')->nullable()->after('deadline_at');
            $table->string('vendor_email_sent_to', 255)->nullable()->after('vendor_email_sent_at');
            $table->foreignId('vendor_email_sent_by')->nullable()->after('vendor_email_sent_to')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('vendor_email_sent_count')->default(0)->after('vendor_email_sent_by');
            $table->json('vendor_email_sent_asset_ids')->nullable()->after('vendor_email_sent_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_jobs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_email_sent_by');
            $table->dropColumn([
                'vendor_email_sent_at',
                'vendor_email_sent_to',
                'vendor_email_sent_count',
                'vendor_email_sent_asset_ids',
            ]);
        });
    }
};

