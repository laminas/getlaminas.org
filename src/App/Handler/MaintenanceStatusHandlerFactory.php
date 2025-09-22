<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;

use function assert;
use function file_get_contents;
use function getcwd;
use function is_array;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

final class MaintenanceStatusHandlerFactory
{
    public function __invoke(ContainerInterface $container): MaintenanceStatusHandler
    {
        $rawData = file_get_contents(
            sprintf(
                '%s/%s',
                getcwd() . MaintenanceOverviewHandler::CUSTOM_PROPERTIES_DIRECTORY,
                MaintenanceOverviewHandler::CUSTOM_PROPERTIES_FILE
            )
        );

        $repositoryData = json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($repositoryData));

        return new MaintenanceStatusHandler($repositoryData);
    }
}
