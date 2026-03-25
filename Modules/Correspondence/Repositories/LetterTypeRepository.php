<?php

declare(strict_types=1);

namespace Modules\Correspondence\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Correspondence\Models\LetterType;

class LetterTypeRepository extends BaseCrudRepository
{
    public function __construct(LetterType $model)
    {
        parent::__construct($model);
    }
}
