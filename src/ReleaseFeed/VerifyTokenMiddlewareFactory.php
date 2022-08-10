<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Container\ContainerInterface;

use function assert;
use function is_string;

class VerifyTokenMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): VerifyTokenMiddleware
    {
        $token = $container->get('config')['release-feed']['verification_token'] ?? '';
        assert(is_string($token) && '' !== $token);

        $responseFactory = $container->get(ProblemDetailsResponseFactory::class);
        assert($responseFactory instanceof ProblemDetailsResponseFactory);

        return new VerifyTokenMiddleware($token, $responseFactory);
    }
}
