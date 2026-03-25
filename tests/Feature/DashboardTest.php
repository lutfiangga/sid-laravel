<?php

use App\Models\User;
use Modules\PublicService\Models\Announcement;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard and see stats', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Total Penduduk')
        ->assertSee('Total Keluarga')
        ->assertSee('Permohonan Surat');
});

test('dashboard displays latest announcements', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Announcement::factory()->create([
        'title' => 'Pengumuman Penting Dashboard',
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertSee('Pengumuman Penting Dashboard');
});
