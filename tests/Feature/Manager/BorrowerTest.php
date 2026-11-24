<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('le responsable peut voir la liste des emprunteurs', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    User::factory()->create(['name' => 'Manon Lejeune', 'role' => 'borrower']);

    $this->actingAs($manager)
        ->get('/manager/borrowers')
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('manager/BorrowersIndex')
                ->has('borrowers', 1)
                ->where('borrowers.0.name', 'Manon Lejeune')
        );
});

test('le responsable peut creer un emprunteur avec contact obligatoire', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    $this->actingAs($manager)
        ->post('/manager/borrowers', [
            'name' => 'Aylin Demir',
            'email' => 'aylin.demir@ifosup.be',
            'phone' => '0475/12.34.56',
            'borrower_type' => 'student',
            'section_or_service' => 'BES Webdev 2e',
            'password' => 'secret123',
        ])
        ->assertRedirect('/manager/borrowers');

    $this->assertDatabaseHas('users', [
        'email' => 'aylin.demir@ifosup.be',
        'phone' => '0475/12.34.56',
        'role' => 'borrower',
    ]);
});

test('la creation echoue si le numero de telephone manque', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    $this->actingAs($manager)
        ->post('/manager/borrowers', [
            'name' => 'Aylin Demir',
            'email' => 'aylin.demir@ifosup.be',
            'phone' => '',
            'borrower_type' => 'student',
            'section_or_service' => 'BES Webdev 2e',
            'password' => 'secret123',
        ])
        ->assertSessionHasErrors('phone');
});

test('un emprunteur ne peut pas gerer les comptes emprunteurs', function () {
    $borrower = User::factory()->create(['role' => 'borrower']);

    $this->actingAs($borrower)
        ->get('/manager/borrowers')
        ->assertForbidden();
});
