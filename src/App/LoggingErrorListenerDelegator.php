<?php

declare(strict_types=1);

namespace App;

use Laminas\Stratigility\Middleware\ErrorHandler;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggingErrorListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ): ErrorHandler {
        $errorHandler = $callback();
        $errorHandler->attachListener(
            new LoggingErrorListener($container->get(LoggerInterface::class))
        );
        return $errorHandler;
    }
}
