<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace GetLaminas\Blog\Handler;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Handler\NotFoundHandler;

class FeedHandlerFactory
{
    public function __invoke(ContainerInterface $container) : FeedHandler
    {
        return new FeedHandler(
            $container->get(NotFoundHandler::class)
        );
    }
}
