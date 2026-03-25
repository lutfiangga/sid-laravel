<?php

declare(strict_types=1);

namespace Modules\Population\Services;

use App\Core\Base\BaseCrudService;
use Modules\Population\Contracts\Repositories\PendudukRepositoryInterface;
use Modules\Population\Contracts\Services\PendudukServiceInterface;

class PendudukService extends BaseCrudService implements PendudukServiceInterface
{
    public function __construct(PendudukRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function beforeCreate(array $data): array
    {
        if (isset($data['nik']) && $this->repository->findByNik($data['nik'])) {
            throw new \Exception('NIK ' . $data['nik'] . ' sudah terdaftar.');
        }

        return $data;
    }
}
