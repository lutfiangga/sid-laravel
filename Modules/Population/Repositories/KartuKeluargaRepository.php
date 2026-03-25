<?php

declare(strict_types=1);

namespace Modules\Population\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Population\Contracts\Repositories\KartuKeluargaRepositoryInterface;
use Modules\Population\Models\KartuKeluarga;

class KartuKeluargaRepository extends BaseCrudRepository implements KartuKeluargaRepositoryInterface
{
    public function __construct(KartuKeluarga $model)
    {
        parent::__construct($model);
    }
}
