<?php

declare(strict_types=1);

namespace Modules\Correspondence\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Correspondence\Models\LetterRequest;

class LetterRequestRepository extends BaseCrudRepository
{
    public function __construct(LetterRequest $model)
    {
        parent::__construct($model);
    }
}
