<?php

declare(strict_types=1);

namespace GetLaminas\Security;

use App\ContentParser\Parser;

class AdvisoryFactory
{
    public function __invoke(): Advisory
    {
        return new Advisory(new Parser());
    }
}
