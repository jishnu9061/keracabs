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
        Schema::create('route_fares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_stop_id')->constrained()->onDelete('cascade');
            $table->decimal('fare_amount', 10, 2);
            $table->tinyInteger('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_fares');
    }
};
