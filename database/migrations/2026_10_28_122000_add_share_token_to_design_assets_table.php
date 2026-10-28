<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\DesignAsset;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('design_assets', function (Blueprint $table) {
            $table->string('share_token', 64)->nullable()->unique()->after('file_name');
        });

        // Populate existing assets with unique tokens
        DesignAsset::all()->each(function ($asset) {
            $asset->share_token = Str::random(32);
            $asset->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_assets', function (Blueprint $table) {
            $table->dropColumn('share_token');
        });
    }
};

