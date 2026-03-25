<?php

declare(strict_types=1);

use App\Core\Base\BaseCrudRepository;
use App\Core\Base\BaseCrudService;
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

    $repository = new class($model) extends BaseCrudRepository {};

    $this->service = new class($repository) extends BaseCrudService
    {
        public bool $beforeCreateCalled = false;

        public bool $afterCreateCalled = false;

        public bool $beforeUpdateCalled = false;

        public bool $afterUpdateCalled = false;

        public bool $beforeDeleteCalled = false;

        public bool $afterDeleteCalled = false;

        protected function beforeCreate(array $data): array
        {
            $this->beforeCreateCalled = true;

            return $data;
        }

        protected function afterCreate(\Illuminate\Database\Eloquent\Model $model): void
        {
            $this->afterCreateCalled = true;
        }

        protected function beforeUpdate(string $id, array $data): array
        {
            $this->beforeUpdateCalled = true;

            return $data;
        }

        protected function afterUpdate(\Illuminate\Database\Eloquent\Model $model): void
        {
            $this->afterUpdateCalled = true;
        }

        protected function beforeDelete(string $id): void
        {
            $this->beforeDeleteCalled = true;
        }

        protected function afterDelete(string $id): void
        {
            $this->afterDeleteCalled = true;
        }
    };
});

it('can create a record via service', function () {
    $record = $this->service->create(['name' => 'Service Test']);

    expect($record->name)->toBe('Service Test')
        ->and($record->id)->not->toBeNull();
});

it('calls beforeCreate and afterCreate hooks', function () {
    $this->service->create(['name' => 'Hook Test']);

    expect($this->service->beforeCreateCalled)->toBeTrue()
        ->and($this->service->afterCreateCalled)->toBeTrue();
});

it('can get a record by id via service', function () {
    $record = $this->service->create(['name' => 'Find Via Service']);

    $found = $this->service->getById($record->id);

    expect($found->name)->toBe('Find Via Service');
});

it('can get all records via service', function () {
    $this->service->create(['name' => 'Service Item 1']);
    $this->service->create(['name' => 'Service Item 2']);

    $all = $this->service->getAll();

    expect($all)->toHaveCount(2);
});

it('can get paginated records via service', function () {
    for ($i = 1; $i <= 20; $i++) {
        $this->service->create(['name' => "Paginated {$i}"]);
    }

    $paginated = $this->service->getPaginated(10);

    expect($paginated->count())->toBe(10)
        ->and($paginated->total())->toBe(20);
});

it('can update a record via service', function () {
    $record = $this->service->create(['name' => 'Before Update']);

    $updated = $this->service->update($record->id, ['name' => 'After Update']);

    expect($updated->name)->toBe('After Update');
});

it('calls beforeUpdate and afterUpdate hooks', function () {
    $record = $this->service->create(['name' => 'Update Hook Test']);

    $this->service->update($record->id, ['name' => 'Updated']);

    expect($this->service->beforeUpdateCalled)->toBeTrue()
        ->and($this->service->afterUpdateCalled)->toBeTrue();
});

it('can delete a record via service', function () {
    $record = $this->service->create(['name' => 'Delete Via Service']);

    $result = $this->service->delete($record->id);

    expect($result)->toBeTrue();
});

it('calls beforeDelete and afterDelete hooks', function () {
    $record = $this->service->create(['name' => 'Delete Hook Test']);

    $this->service->delete($record->id);

    expect($this->service->beforeDeleteCalled)->toBeTrue()
        ->and($this->service->afterDeleteCalled)->toBeTrue();
});
