<?php

declare(strict_types=1);

namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class AccessLoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $logger             = new Logger('getlaminas');
        $logger->pushHandler(new StreamHandler(
            'php://stderr',
            Logger::INFO,
            $bubble         = true,
            $expandNewLines = true
        ));
        $logger->pushProcessor(new PsrLogMessageProcessor());
        return $logger;
    }
}
