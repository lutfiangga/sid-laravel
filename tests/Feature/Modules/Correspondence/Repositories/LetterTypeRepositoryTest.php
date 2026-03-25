<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Correspondence\Repositories;

use Modules\Correspondence\Models\LetterType;
use Modules\Correspondence\Repositories\LetterTypeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('letter type repository can get all types', function () {
    LetterType::factory()->count(3)->create();
    $repo = app(LetterTypeRepository::class);

    expect($repo->all())->toHaveCount(3);
});
