<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Payments\PaymentStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('unique_id');
            $table->foreignId('user_id')->nullable(true)->references('id')->on('users')->onDelete('cascade');
            $table->double('amount');
            $table->enum('status', PaymentStatus::values())->default(PaymentStatus::PENDING);
            $table->string('currency_key');
            $table->timestamp('status_update_at')->nullable(true);
            $table->foreignId('status_update_by')->nullable(true)->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
