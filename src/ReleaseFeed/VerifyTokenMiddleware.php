<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function preg_match;

final class VerifyTokenMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $token,
        private readonly ProblemDetailsResponseFactory $problemFactory
    ) {
    }

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $header = $request->getHeaderLine('Authorization');
        if (! preg_match('/^token ' . $this->token . '/i', $header)) {
            return $this->problemFactory->createResponse($request, 401, 'Missing or invalid authentication token');
        }

        return $handler->handle($request);
    }
}
