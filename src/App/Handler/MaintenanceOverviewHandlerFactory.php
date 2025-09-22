<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;
use function file_get_contents;
use function getcwd;
use function is_array;
use function is_string;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

final class MaintenanceOverviewHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): MaintenanceOverviewHandler
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

        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        $lastUpdated = $repositoryData['last_updated'];
        assert(is_string($lastUpdated));
        unset($repositoryData['last_updated']);

        return new MaintenanceOverviewHandler($repositoryData, $lastUpdated, $renderer);
    }
}
