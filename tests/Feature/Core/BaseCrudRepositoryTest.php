<?php

declare(strict_types=1);

use App\Core\Base\BaseCrudRepository;
use App\Core\Base\BaseModel;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    if (! Schema::hasTable('test_items')) {
        Schema::create('test_items', function ($table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('status')->default('draft');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    $model = new class extends BaseModel
    {
        protected $table = 'test_items';

        protected $fillable = ['name', 'status', 'description'];
    };

    $this->repository = new class($model) extends BaseCrudRepository {};
});

it('can create a record', function () {
    $record = $this->repository->create(['name' => 'Test Item']);

    expect($record->name)->toBe('Test Item')
        ->and($record->id)->not->toBeNull();
});

it('can find a record by id', function () {
    $record = $this->repository->create(['name' => 'Find Me']);

    $found = $this->repository->find($record->id);

    expect($found)->not->toBeNull()
        ->and($found->name)->toBe('Find Me');
});

it('can find or fail a record', function () {
    $record = $this->repository->create(['name' => 'Find Or Fail']);

    $found = $this->repository->findOrFail($record->id);

    expect($found->name)->toBe('Find Or Fail');
});

it('throws exception when findOrFail does not find record', function () {
    $this->repository->findOrFail('non-existent-uuid');
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

it('can get all records', function () {
    $this->repository->create(['name' => 'Item 1']);
    $this->repository->create(['name' => 'Item 2']);
    $this->repository->create(['name' => 'Item 3']);

    $all = $this->repository->all();

    expect($all)->toHaveCount(3);
});

it('can paginate records', function () {
    for ($i = 1; $i <= 20; $i++) {
        $this->repository->create(['name' => "Item {$i}"]);
    }

    $paginated = $this->repository->paginate(10);

    expect($paginated->count())->toBe(10)
        ->and($paginated->total())->toBe(20);
});

it('can update a record', function () {
    $record = $this->repository->create(['name' => 'Original Name']);

    $updated = $this->repository->update($record->id, ['name' => 'Updated Name']);

    expect($updated->name)->toBe('Updated Name');
});

it('can soft delete a record', function () {
    $record = $this->repository->create(['name' => 'Delete Me']);

    $result = $this->repository->delete($record->id);

    expect($result)->toBeTrue()
        ->and($this->repository->find($record->id))->toBeNull();
});

it('can force delete a record', function () {
    $record = $this->repository->create(['name' => 'Force Delete Me']);
    $id = $record->id;

    $this->repository->delete($id);
    $result = $this->repository->forceDelete($id);

    expect($result)->toBeTrue();
});

it('can restore a soft-deleted record', function () {
    $record = $this->repository->create(['name' => 'Restore Me']);
    $id = $record->id;

    $this->repository->delete($id);
    $restored = $this->repository->restore($id);

    expect($restored->name)->toBe('Restore Me')
        ->and($this->repository->find($id))->not->toBeNull();
});
