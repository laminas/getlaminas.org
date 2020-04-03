<?php

declare(strict_types=1);

namespace App;

use Phly\EventDispatcher\EventDispatcher;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class EventDispatcherFactory
{
    public function __invoke(ContainerInterface $container): EventDispatcherInterface
    {
        return new EventDispatcher(
            $container->get(ListenerProviderInterface::class)
        );
    }
}
