<?php

declare(strict_types=1);

namespace Modules\Correspondence\Services;

use App\Core\Base\BaseCrudService;
use Modules\Correspondence\Repositories\LetterTypeRepository;

class LetterTypeService extends BaseCrudService
{
    public function __construct(LetterTypeRepository $repository)
    {
        parent::__construct($repository);
    }
}
