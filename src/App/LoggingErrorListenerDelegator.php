<?php

namespace App;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Laminas\Stratigility\Middleware\ErrorHandler;

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
