<?php

declare(strict_types=1);

use App\Core\Base\BaseModel;
use Illuminate\Support\Facades\Schema;

// Create a concrete test model for BaseModel testing
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

    $this->testModelClass = new class extends BaseModel
    {
        protected $table = 'test_items';

        protected $fillable = ['name', 'status', 'description'];

        protected array $searchable = ['name', 'description'];

        protected array $filterable = ['status'];
    };
});

it('generates a UUID primary key on creation', function () {
    $model = $this->testModelClass->newInstance();
    $model->name = 'Test Item';
    $model->save();

    expect($model->id)->not->toBeNull()
        ->and($model->id)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

it('supports soft deletes', function () {
    $model = $this->testModelClass->newInstance();
    $model->name = 'Soft Delete Test';
    $model->save();
    $id = $model->id;

    $model->delete();

    expect($this->testModelClass->find($id))->toBeNull()
        ->and($this->testModelClass->withTrashed()->find($id))->not->toBeNull();
});

it('auto-fills created_by when authenticated', function () {
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    $model = $this->testModelClass->newInstance();
    $model->name = 'Auth User Test';
    $model->save();

    expect($model->created_by)->toBe($user->id);
});

it('auto-fills updated_by when updating as authenticated user', function () {
    $user = App\Models\User::factory()->create();

    $model = $this->testModelClass->newInstance();
    $model->name = 'Before Update';
    $model->save();

    $this->actingAs($user);
    $model->name = 'After Update';
    $model->save();

    expect($model->fresh()->updated_by)->toBe($user->id);
});

it('auto-fills deleted_by on soft delete when authenticated', function () {
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    $model = $this->testModelClass->newInstance();
    $model->name = 'Delete Tracking Test';
    $model->save();

    $model->delete();

    expect($this->testModelClass->withTrashed()->find($model->id)->deleted_by)->toBe($user->id);
});

it('returns searchable columns', function () {
    expect($this->testModelClass->getSearchable())->toBe(['name', 'description']);
});

it('returns filterable columns', function () {
    expect($this->testModelClass->getFilterable())->toBe(['status']);
});
