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
        Schema::create('user_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('key', 100)->index();
            $table->string('type', 100)->nullable();
            $table->longText('value')->nullable();
            $table->softDeletes();
            $table->string('verified_at')->default('')->nullable();
            $table->boolean('is_active')->default(True);
            $table->string('delete_type')->default('none'); // onDeleteCascade - User Delete - Admin Delete - System Delete
            $table->text('note')->default('')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'key']); // هر key فقط یکبار برای هر کاربر
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_metas');
    }
};
