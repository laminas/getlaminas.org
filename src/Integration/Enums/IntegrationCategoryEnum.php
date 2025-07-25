<?php

declare(strict_types=1);

namespace GetLaminas\Integration\Enums;

enum IntegrationCategoryEnum: string
{
    case Skeleton    = 'skeleton';
    case Integration = 'integration';
    case Tool        = 'tool';
}
