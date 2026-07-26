<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserExperienceTest extends TestCase
{
    use RefreshDatabase;

    // Pesan validasi form tiket tampil dalam bahasa Indonesia.
    public function test_ticket_validation_message_uses_indonesian_language(): void
    {
        $reporter = User::factory()->create();
        Application::factory()->create();

        $this
            ->actingAs($reporter)
            ->post(route('tickets.store'), [
                'title' => '',
                'description' => '',
            ])
            ->assertSessionHasErrors([
                'application_id' => 'aplikasi wajib diisi.',
                'title' => 'judul kendala wajib diisi.',
                'category' => 'kategori wajib diisi.',
                'description' => 'deskripsi kendala wajib diisi.',
            ]);
    }

    // Halaman yang tidak ada memberikan penjelasan dan jalan kembali.
    public function test_not_found_page_uses_sipena_error_design(): void
    {
        $this
            ->get('/halaman-yang-tidak-tersedia')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee('SIPENA');
    }
}
