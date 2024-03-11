<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            $table->renameColumn('markup_path', 'markup_listing_path');
        });

        Schema::table('podcasts', function (Blueprint $table) {
            $table->json('markup_detail_paths')->nullable()->after('markup_listing_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            $table->renameColumn('markup_listing_path', 'markup_path');
            $table->dropColumn('markup_detail_paths');
        });
    }
};
