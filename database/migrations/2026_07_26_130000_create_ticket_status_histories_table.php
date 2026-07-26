<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat penyimpanan riwayat perubahan status tiket.
     */
    public function up(): void
    {
        Schema::create('ticket_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete();
            $table->foreignId('changed_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            // Mengoptimalkan pengambilan timeline dan penyaringan status.
            $table->index(['ticket_id', 'created_at']);
            $table->index('to_status');
        });

        // Membuat riwayat awal untuk tiket yang telah ada sebelum migration ini.
        DB::table('tickets')
            ->select(['id', 'reporter_id', 'status', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->eachById(function (object $ticket): void {
                DB::table('ticket_status_histories')->insert([
                    'ticket_id' => $ticket->id,
                    'changed_by' => $ticket->reporter_id,
                    'from_status' => null,
                    'to_status' => $ticket->status,
                    'notes' => 'Riwayat awal tiket.',
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                ]);
            });
    }

    /**
     * Menghapus tabel riwayat status tiket.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_status_histories');
    }
};
