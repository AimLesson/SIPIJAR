<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Pastikan kolom dibuat nullable terlebih dahulu agar data yang belum valid tetap diterima
            if (!Schema::hasColumn('events', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable() // penting agar existing row tidak error
                    ->after('id')
                    ->constrained('users')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop foreign key terlebih dahulu sebelum drop kolom
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
