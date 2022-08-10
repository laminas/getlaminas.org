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

class ReceiveFeedItemHandlerFactory
{
    public function __invoke(ContainerInterface $container): ReceiveFeedItemHandler
    {
        $env = new Environment();
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new GithubFlavoredMarkdownExtension());
        $env->addExtension(new TableExtension());

        return new ReceiveFeedItemHandler(
            $container->get('config')['release-feed']['feed-file'],
            new MarkdownConverter($env),
            $container->get(ResponseFactoryInterface::class),
            $container->get(ProblemDetailsResponseFactory::class)
        );
    }
}
