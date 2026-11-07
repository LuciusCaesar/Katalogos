<?php

use App\Models\User;

test('guests can visit the landing page', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Katalogos');
});

test('authenticated users are redirected to the dashboard from the landing page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('home'));

    $response->assertRedirect(route('dashboard'));
});
