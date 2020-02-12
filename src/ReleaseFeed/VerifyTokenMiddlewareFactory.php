<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Container\ContainerInterface;

class VerifyTokenMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : VerifyTokenMiddleware
    {
        return new VerifyTokenMiddleware(
            $container->get('config')['release-feed']['verification_token'],
            $container->get(ProblemDetailsResponseFactory::class)
        );
    }
}
