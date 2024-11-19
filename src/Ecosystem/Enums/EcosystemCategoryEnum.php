<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Enums;

enum EcosystemCategoryEnum: string
{
    case Skeleton    = 'skeleton';
    case Integration = 'integration';
    case Tool        = 'tool';
}
