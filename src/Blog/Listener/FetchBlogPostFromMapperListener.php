<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Listener;

use GetLaminas\Blog\BlogPost;
use GetLaminas\Blog\FetchBlogPostEvent;
use GetLaminas\Blog\Mapper\MapperInterface;

class FetchBlogPostFromMapperListener
{
    public function __construct(private readonly MapperInterface $mapper)
    {
    }

    public function __invoke(FetchBlogPostEvent $event): void
    {
        $post = $this->mapper->fetch($event->id());

        if (! $post instanceof BlogPost) {
            return;
        }

        $event->provideBlogPost($post);
    }
}
