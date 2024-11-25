<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Enums;

enum EcosystemTypeEnum: string
{
    case Library     = 'library';
    case Project     = 'project';
    case Metapackage = 'metapackage';
    case Plugin      = 'composer-plugin';
}
