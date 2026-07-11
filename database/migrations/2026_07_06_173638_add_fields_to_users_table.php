<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('executor');
        $table->boolean('is_active')->default(true);
        $table->string('position')->nullable();
        $table->string('phone')->nullable()->unique();
        $table->string('telegram_id')->nullable()->unique();
        $table->json('settings')->nullable();
        $table->timestamp('last_active_at')->nullable();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'role', 'is_active', 'position',
            'phone', 'telegram_id', 'settings', 'last_active_at'
        ]);
    });
}
};
