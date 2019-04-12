<?php

namespace App;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

class AccessLoggerFactory
{
    public function __invoke(ContainerInterface $container) : LoggerInterface
    {
        $logger = new Logger('getlaminas');
        $logger->pushHandler(new StreamHandler(
            'php://stderr',
            Logger::INFO,
            $bubble = true,
            $expandNewLines = true
        ));
        $logger->pushProcessor(new PsrLogMessageProcessor());
        return $logger;
    }
}
