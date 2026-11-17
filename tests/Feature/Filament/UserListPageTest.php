<?php

declare(strict_types=1);

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Airline;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

// The ident column reads the airline's ICAO. Lazy loading is blocked outside
// production and only trips once a page holds more than one record, so the
// list needs several pilots to show the relation is eager-loaded.
it('lists pilots with their idents when lazy loading is prevented', function (): void {
    $this->seed(RolesPermissionsSeeder::class);
    Model::preventLazyLoading();

    $airline = Airline::factory()->create(['icao' => 'XYZ']);
    $admin = createAdminUser(['airline_id' => $airline->id, 'pilot_id' => 1]);
    $pilots = User::factory()->count(2)->sequence(
        ['pilot_id' => 2],
        ['pilot_id' => 3],
    )->create(['airline_id' => $airline->id]);

    $this->actingAs($admin);

    Livewire::test(ListUsers::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($pilots->push($admin))
        ->assertSee(['XYZ0001', 'XYZ0002', 'XYZ0003']);
});

it('serves the admin users page to an admin', function (): void {
    $this->seed(RolesPermissionsSeeder::class);
    Model::preventLazyLoading();

    $airline = Airline::factory()->create();
    $admin = createAdminUser(['airline_id' => $airline->id]);
    User::factory()->count(2)->create(['airline_id' => $airline->id]);

    $this->actingAs($admin)
        ->get('/admin/users')
        ->assertOk();
});
