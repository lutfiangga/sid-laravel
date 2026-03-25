<?php

declare(strict_types=1);

namespace Modules\LetterTemplate\Services;

use App\Core\Base\BaseCrudService;
use Modules\LetterTemplate\Repositories\LetterTemplateRepository;

class LetterTemplateService extends BaseCrudService
{
    public function __construct(LetterTemplateRepository $repository)
    {
        parent::__construct($repository);
    }
}
