<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace GetLaminas\Blog\Listener;

use GetLaminas\Blog\BlogPost;
use GetLaminas\Blog\FetchBlogPostEvent;
use GetLaminas\Blog\Mapper\MapperInterface;

class FetchBlogPostFromMapperListener
{
    /**
     * @var MapperInterface
     */
    private $mapper;

    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function __invoke(FetchBlogPostEvent $event) : void
    {
        $post = $this->mapper->fetch($event->id());

        if (! $post instanceof BlogPost) {
            return;
        }

        $event->provideBlogPost($post);
    }
}
