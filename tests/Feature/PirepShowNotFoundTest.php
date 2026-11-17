<?php

declare(strict_types=1);

use App\Models\User;

it('sends a pilot back to their PIREPs when the PIREP does not exist', function (): void {
    $this->actingAs(User::factory()->create())
        ->get('/pireps/doesnotexist')
        ->assertRedirect(route('frontend.pireps.index'));
});
