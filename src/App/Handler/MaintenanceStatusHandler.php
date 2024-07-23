<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use const JSON_PRETTY_PRINT;

class MaintenanceStatusHandler implements RequestHandlerInterface
{
    public function __construct(private array $repositoryData)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            data: $this->repositoryData,
            encodingOptions: JsonResponse::DEFAULT_JSON_FLAGS | JSON_PRETTY_PRINT
        );
    }
}
