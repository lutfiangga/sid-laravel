<?php

use Modules\PublicService\Models\Announcement;
use Modules\PublicService\Services\AnnouncementService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create an announcement via service', function () {
    $user = User::factory()->create();
    $service = app(AnnouncementService::class);
    
    $data = [
        'title' => 'Rapat Tahunan',
        'slug' => 'rapat-tahunan',
        'content' => 'Isi rapat tahunan',
        'is_published' => true,
        'author_id' => $user->id,
    ];
    
    $announcement = $service->create($data);
    
    expect($announcement)->toBeInstanceOf(Announcement::class)
        ->title->toBe('Rapat Tahunan');
        
    $this->assertDatabaseHas('announcements', [
        'slug' => 'rapat-tahunan',
        'is_published' => 1,
    ]);
});

it('can paginate announcements via service', function () {
    Announcement::factory()->count(5)->create();
    
    $service = app(AnnouncementService::class);
    $paginated = $service->getPaginated(perPage: 10);
    
    expect($paginated->count())->toBe(5);
});
