<?php

declare(strict_types=1);

namespace GetLaminas\Integration\Enums;

enum IntegrationTypeEnum: string
{
    case Library     = 'library';
    case Project     = 'project';
    case Metapackage = 'metapackage';
    case Plugin      = 'composer-plugin';
}
