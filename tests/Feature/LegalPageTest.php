<?php

use Inertia\Testing\AssertableInertia as Assert;

test('legal page is public', function () {
    $this->get('/mentions-legales')
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page->component('public/Legal'));
});
