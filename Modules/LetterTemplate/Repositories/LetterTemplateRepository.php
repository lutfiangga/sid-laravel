<?php

declare(strict_types=1);

namespace Modules\LetterTemplate\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\LetterTemplate\Models\LetterTemplate;

class LetterTemplateRepository extends BaseCrudRepository
{
    public function __construct(LetterTemplate $model)
    {
        parent::__construct($model);
    }
}
