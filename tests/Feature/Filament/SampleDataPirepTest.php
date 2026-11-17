<?php

declare(strict_types=1);

use App\Enums\PirepPhase;
use App\Filament\Resources\Pireps\PirepResource;
use App\Models\Pirep;
use Database\Seeders\BaseDataSeeder;
use Database\Seeders\RolesPermissionsSeeder;
use Database\Seeders\SampleDataSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    $this->seed(BaseDataSeeder::class);
    $this->seed(SampleDataSeeder::class);
});

it('seeds every sample PIREP with a valid phase', function (): void {
    $invalid = DB::table('pireps')
        ->whereNotNull('status')
        ->pluck('status', 'id')
        ->reject(fn (string $status): bool => PirepPhase::tryFrom($status) instanceof PirepPhase);

    expect($invalid->all())->toBe([]);
});

it('opens the seeded sample PIREPs in admin edit', function (string $id): void {
    $this->seed(RolesPermissionsSeeder::class);

    $this->actingAs(createAdminUser())
        ->get(PirepResource::getUrl('edit', ['record' => Pirep::findOrFail($id)]))
        ->assertOk();
})->with(['pirepid_1', 'pirepid_4']);
