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
        if (! Schema::hasTable('fg_itam_license_assets')) {
            Schema::create('fg_itam_license_assets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('license_id')->nullable()->index();
                $table->string('software_name', 150)->index();
                $table->string('asset_id', 50)->index();
                $table->date('assigned_date')->nullable();
                $table->string('status', 50)->default('Active');
                $table->timestamps();

                $table->unique(['software_name', 'asset_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fg_itam_license_assets');
    }
};
