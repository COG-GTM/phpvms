<?php

declare(strict_types=1);

use App\Models\User;
use Igaster\LaravelTheme\Facades\Theme;
use Inertia\Testing\AssertableInertia as Assert;

it('sends a pilot back to their PIREPs when the PIREP does not exist', function (): void {
    $this->actingAs(User::factory()->create())
        ->get('/pireps/doesnotexist')
        ->assertRedirect(route('frontend.pireps.index'))
        ->assertSessionHas('flash_notification');
});

// Sessions are stored as JSON (config/session.php `serialization`), so on the
// request after the redirect the laracasts/flash messages come back as plain
// arrays rather than Message objects. The test session never serializes, so it
// is seeded with that decoded shape directly.
it('shows the not-found message on the logbook after the redirect', function (): void {
    Theme::set('skylight');
    updateSetting('general.theme', 'skylight');

    $this->actingAs(User::factory()->create())
        ->withSession(['flash_notification' => [
            ['title' => null, 'message' => 'Pirep not found', 'level' => 'danger', 'important' => false, 'overlay' => false],
        ]])
        ->get('/pireps')
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('Pireps/Index', false)
            ->where('flash.error', 'Pirep not found'));
});
