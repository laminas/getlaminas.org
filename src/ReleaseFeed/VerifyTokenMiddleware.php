<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class VerifyTokenMiddleware implements MiddlewareInterface
{
    /** ProblemDetailsResponseFactory */
    private $problemFactory;

    /** @var string */
    private $token;

    public function __construct(string $token, ProblemDetailsResponseFactory $problemFactory)
    {
        $this->token = $token;
        $this->problemFactory = $problemFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $header = $request->getHeaderLine('Authorization');
        if (! preg_match('/^token ' . $this->token . '/i', $header)) {
            return $this->problemFactory->createResponse($request, 401, 'Missing or invalid authentication token');
        }

        return $handler->handle($request);
    }
}
