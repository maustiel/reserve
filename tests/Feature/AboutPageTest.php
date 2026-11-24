<?php

use Inertia\Testing\AssertableInertia as Assert;

test('about page is public', function () {
    $this->get('/a-propos')
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page->component('public/About'));
});
