<?php

declare(strict_types=1);

namespace Modules\Population\Services;

use App\Core\Base\BaseCrudService;
use Modules\Population\Contracts\Repositories\KartuKeluargaRepositoryInterface;
use Modules\Population\Contracts\Services\KartuKeluargaServiceInterface;

class KartuKeluargaService extends BaseCrudService implements KartuKeluargaServiceInterface
{
    public function __construct(KartuKeluargaRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
