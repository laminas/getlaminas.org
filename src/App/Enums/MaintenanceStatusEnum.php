<?php

declare(strict_types=1);

namespace App\Enums;

enum MaintenanceStatusEnum: string
{
    case ACTIVE           = 'active';
    case DISCONTINUED     = 'discontinued';
    case MAINTENANCE_ONLY = 'maintenance-only';
    case SECURITY_ONLY    = 'security-only';

    public function label(): ?string
    {
        return match ($this) {
            MaintenanceStatusEnum::ACTIVE           => 'Active',
            MaintenanceStatusEnum::DISCONTINUED     => 'Discontinued',
            MaintenanceStatusEnum::MAINTENANCE_ONLY => 'Maintenance only',
            MaintenanceStatusEnum::SECURITY_ONLY    => 'Security only',
        };
    }
}
