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

class CustomPropertiesHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CustomPropertiesHandler
    {
        $rawData        = file_get_contents(sprintf('%s/%s', getcwd() . "/public/share", 'properties.json'));
        $repositoryData = json_decode($rawData, true);
        assert(is_array($repositoryData));

        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        $lastUpdated = $repositoryData['last_updated'];
        assert(is_string($lastUpdated));
        unset($repositoryData['last_updated']);

        return new CustomPropertiesHandler($repositoryData, $lastUpdated, $renderer);
    }
}
