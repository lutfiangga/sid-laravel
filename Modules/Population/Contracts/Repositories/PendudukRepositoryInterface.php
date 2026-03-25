<?php

declare(strict_types=1);

namespace Modules\Population\Contracts\Repositories;

use App\Core\Contracts\BaseCrudRepositoryInterface;

interface PendudukRepositoryInterface extends BaseCrudRepositoryInterface
{
    public function findByNik(string $nik): ?\Illuminate\Database\Eloquent\Model;
}
