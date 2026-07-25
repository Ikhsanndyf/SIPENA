<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DeveloperAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'developer'])
            ->get('/test/developer-only', function () {
                return response('OK');
            });
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/test/developer-only')
            ->assertRedirect(route('login'));
    }

    public function test_reporter_cannot_access_developer_route(): void
    {
        $reporter = User::factory()->create();

        /** @var User $reporter */
        $this->actingAs($reporter)
            ->get('/test/developer-only')
            ->assertForbidden();
    }

    public function test_developer_can_access_developer_route(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)
            ->get('/test/developer-only')
            ->assertOk()
            ->assertSee('OK');
    }
}
