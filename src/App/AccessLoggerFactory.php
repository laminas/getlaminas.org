<?php

declare(strict_types=1);

namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

class AccessLoggerFactory
{
    public function __invoke(): LoggerInterface
    {
        $logger = new Logger('getlaminas');
        $logger->pushHandler(new StreamHandler(
            stream: 'php://stderr',
            level: Level::Debug,
            bubble: true,
        ));
        $logger->pushProcessor(new PsrLogMessageProcessor());
        return $logger;
    }
}
