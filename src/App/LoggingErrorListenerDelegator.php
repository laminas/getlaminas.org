<?php

declare(strict_types=1);

namespace App;

use Laminas\Stratigility\Middleware\ErrorHandler;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function assert;

class LoggingErrorListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ): ErrorHandler {
        $errorHandler = $callback();
        assert($errorHandler instanceof ErrorHandler);

        $logger = $container->get(LoggerInterface::class);
        assert($logger instanceof LoggerInterface);

        $errorHandler->attachListener(new LoggingErrorListener($logger));

        return $errorHandler;
    }
}
