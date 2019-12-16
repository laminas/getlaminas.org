<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

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
    ) : AttachableListenerProvider {
        $provider = $factory();
        $provider->listen(FetchBlogPostEvent::class, $container->get(FetchBlogPostFromMapperListener::class));
        return $provider;
    }
}
