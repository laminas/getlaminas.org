<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Listener;

use GetLaminas\Blog\FetchBlogPostEvent;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class FetchBlogPostEventListenersDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();

        $listener = $container->get(FetchBlogPostFromMapperListener::class);
        assert($listener instanceof FetchBlogPostFromMapperListener);

        $provider->listen(FetchBlogPostEvent::class, $listener);
        return $provider;
    }
}
