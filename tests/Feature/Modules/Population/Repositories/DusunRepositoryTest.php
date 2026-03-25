<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Population\Repositories;

use Modules\Population\Contracts\Repositories\DusunRepositoryInterface;
use Modules\Population\Models\Dusun;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dusun repository can get all dusuns', function () {
    Dusun::factory()->count(5)->create();
    $repository = app(DusunRepositoryInterface::class);

    expect($repository->all())->toHaveCount(5);
});

test('dusun repository can paginate dusuns', function () {
    Dusun::factory()->count(15)->create();
    $repository = app(DusunRepositoryInterface::class);

    $paginated = $repository->paginate(10);
    expect($paginated->total())->toBe(15)
        ->and($paginated->count())->toBe(10);
});
