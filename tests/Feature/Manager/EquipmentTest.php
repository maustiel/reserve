<?php

use App\Models\Category;
use App\Models\Equipment;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('le responsable peut voir et filtrer l inventaire', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $cameras = Category::create(['name' => 'Caméra']);
    $portables = Category::create(['name' => 'Portable']);

    Equipment::create([
        'inventory_code' => 'CAM-01',
        'category_id' => $cameras->id,
        'name' => 'Sony HDR-CX405',
        'condition' => 'good',
        'location' => 'A.04',
    ]);
    Equipment::create([
        'inventory_code' => 'PC-06',
        'category_id' => $portables->id,
        'name' => 'Dell Latitude',
        'condition' => 'out_of_service',
        'location' => 'Atelier',
    ]);

    $this->actingAs($manager)
        ->get('/manager/equipment?category_id='.$cameras->id)
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('manager/EquipmentIndex')
                ->has('equipment', 1)
                ->where('equipment.0.inventory_code', 'CAM-01')
        );
});

test('le responsable peut ajouter un appareil et les doublons sont refuses', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $category = Category::create(['name' => 'Arduino']);

    $payload = [
        'inventory_code' => 'ARD-04',
        'category_id' => $category->id,
        'name' => 'Kit Arduino Uno R4',
        'condition' => 'good',
        'location' => 'A.04',
    ];

    $this->actingAs($manager)->post('/manager/equipment', $payload)->assertRedirect();
    $this->assertDatabaseHas('equipment', ['inventory_code' => 'ARD-04']);

    // Empêche le doublon ARD-04 du tableur client
    $this->actingAs($manager)
        ->post('/manager/equipment', $payload)
        ->assertSessionHasErrors('inventory_code');
});

test('le responsable peut passer un appareil hors service', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $category = Category::create(['name' => 'Trépied']);
    $item = Equipment::create([
        'inventory_code' => 'TRE-03',
        'category_id' => $category->id,
        'name' => 'K&F Concept',
        'condition' => 'good',
        'location' => 'A.04',
    ]);

    $this->actingAs($manager)
        ->put('/manager/equipment/'.$item->id, [
            'inventory_code' => 'TRE-03',
            'category_id' => $category->id,
            'name' => 'K&F Concept',
            'condition' => 'out_of_service',
            'location' => 'Atelier',
        ])
        ->assertRedirect();

    expect($item->fresh()?->condition)->toBe('out_of_service');
});

test('un emprunteur ne peut pas acceder a la gestion de l inventaire', function () {
    $borrower = User::factory()->create(['role' => 'borrower']);

    $this->actingAs($borrower)
        ->get('/manager/equipment')
        ->assertForbidden();
});
