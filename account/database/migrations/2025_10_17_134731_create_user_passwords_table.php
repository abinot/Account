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
        Schema::create('user_passwords', function (Blueprint $table) {
            // 🔹 Primary & Foreign Keys
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');


            // 🔹 Indexed & Limited Columns for Fast Querying
            $table->string('name', 100)
            ->index()
            ->comment('Name of password (e.g., main, SOS, daily)');
            $table->string('key', 70)
            ->comment('Unique key identifier for quick access');
            $table->string('type', 35)
            ->default('general')
            ->index()
            ->comment('Type or purpose of this password');

            // 🔹 Secure Data Columns
            $table->text('value');
            $table->text('value2')->nullable();
            $table->string('encryption_type', 70)->default('AES-256');

            // 🔹 User Guidance & Control
            $table->string('hint', 255)->nullable()->comment('Memory hint shown during login');
            $table->timestamp('expired_at')->nullable();
            $table->boolean('is_active')->default(false)->index();

            // 🔹 Activity & Usage Tracking
            $table->unsignedInteger('usage_count')->default(0);
            $table->unsignedTinyInteger('attempt_count')->default(0);
            $table->timestamp('last_used_at')->nullable();

            // 🔹 System Metadata
            $table->timestamps();

            $table->softDeletes();
            $table->string('delete_type')->default('none'); // onDeleteCascade - User Delete - Admin Delete - System Delete
            $table->text('note')->default('')->nullable();
            // 🔹 Composite Indexes for Performance Optimization
            //$table->unique(['user_id', 'key'], 'user_passwords_user_key_unique');
            $table->index(['id', 'name', 'key']);
            $table->index(['user_id', 'is_active']);
            $table->index(['type', 'expired_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_passwords');
    }
};

