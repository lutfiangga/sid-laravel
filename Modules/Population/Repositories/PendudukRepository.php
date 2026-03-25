<?php

declare(strict_types=1);

namespace Modules\Population\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Population\Contracts\Repositories\PendudukRepositoryInterface;
use Modules\Population\Models\Penduduk;

class PendudukRepository extends BaseCrudRepository implements PendudukRepositoryInterface
{
    public function __construct(Penduduk $model)
    {
        parent::__construct($model);
    }

    public function findByNik(string $nik): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->model->where('nik', $nik)->first();
    }
}
