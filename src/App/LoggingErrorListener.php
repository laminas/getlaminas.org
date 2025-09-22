<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class LoggingErrorListener
{
    /**
     * Log message string with placeholders
     */
    private const string LOG_STRING = '{status} [{method}] {uri}: {error}';

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(
        Throwable $error,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): void {
        $this->logger->error(self::LOG_STRING, [
            'status' => $response->getStatusCode(),
            'method' => $request->getMethod(),
            'uri'    => (string) $request->getUri(),
            'error'  => $error->getMessage(),
        ]);
    }
}
