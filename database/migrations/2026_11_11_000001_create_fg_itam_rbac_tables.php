<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('fg_itam_user_roles')) {
            Schema::create('fg_itam_user_roles', function (Blueprint $table): void {
                $table->id();
                $table->unsignedInteger('usr_id')->unique();
                $table->string('role', 40);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('assigned_by')->nullable();
                $table->string('notes', 255)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('fg_itam_asset_acknowledgements')) {
            Schema::create('fg_itam_asset_acknowledgements', function (Blueprint $table): void {
                $table->id();
                $table->string('asset_id', 50)->index();
                $table->unsignedInteger('usr_id')->index();
                $table->string('employee_name', 150);
                $table->string('status', 30)->default('Acknowledged');
                $table->timestamp('acknowledged_at');
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fg_itam_asset_acknowledgements');
        Schema::dropIfExists('fg_itam_user_roles');
    }
};
