<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_revenue_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->bigInteger('total_revenue_in_cents')->default(0);
            $table->integer('order_count')->default(0);
            $table->integer('line_item_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_revenue_stats');
    }
};
