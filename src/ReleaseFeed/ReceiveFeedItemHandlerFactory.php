<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use RuntimeException;

use function assert;
use function is_string;

final class ReceiveFeedItemHandlerFactory
{
    public function __invoke(ContainerInterface $container): ReceiveFeedItemHandler
    {
        $feedFile = $container->get('config')['release-feed']['feed-file'] ?? '';
        if (! is_string($feedFile) || '' === $feedFile) {
            throw new RuntimeException('Missing release feed file name; must be a non-empty string');
        }

        $env = new Environment();
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new GithubFlavoredMarkdownExtension());
        $env->addExtension(new TableExtension());

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        assert($responseFactory instanceof ResponseFactoryInterface);

        $problemDetailsFactory = $container->get(ProblemDetailsResponseFactory::class);
        assert($problemDetailsFactory instanceof ProblemDetailsResponseFactory);

        return new ReceiveFeedItemHandler(
            $feedFile,
            new MarkdownConverter($env),
            $responseFactory,
            $problemDetailsFactory,
        );
    }
}
