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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('address')->nullable();
            $table->float('radius', 20, 10)->default(1000);
            $table->boolean('is_sub_location')->default(false);
            $table->string('name')->nullable();
            $table->integer('qr_code')->unique();
            $table->string('qr_image_path')->nullable();
            $table->float('lat', 20, 10)->default(10)->nullable();
            $table->float('lng', 20, 10)->default(10)->nullable();
            $table->boolean('can_logtime')->default(true);
            $table->boolean('can_check')->default(true);
            $table->boolean('enable_gps')->default(true);
            $table->boolean('can_break')->default(true);
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses');
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
