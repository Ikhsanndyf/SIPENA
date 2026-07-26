<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat penyimpanan komentar tiket.
     */
    public function up(): void
    {
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->text('body');
            $table->timestamps();

            // Mempercepat pengambilan percakapan berdasarkan urutan waktu.
            $table->index(['ticket_id', 'created_at']);
        });
    }

    /**
     * Menghapus tabel komentar tiket.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
    }
};
