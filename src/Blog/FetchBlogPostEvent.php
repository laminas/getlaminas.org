<?php

declare(strict_types=1);

namespace GetLaminas\Blog;

use JsonSerializable;
use Psr\EventDispatcher\StoppableEventInterface;

class FetchBlogPostEvent implements
    JsonSerializable,
    StoppableEventInterface
{
    /** @var string */
    private $id;

    /** @var null|BlogPost */
    private $post;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'   => $this->id,
            'post' => $this->post,
        ];
    }

    public function isPropagationStopped(): bool
    {
        return null !== $this->post;
    }

    public function blogPost(): ?BlogPost
    {
        return $this->post;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function provideBlogPost(BlogPost $post): void
    {
        $this->post = $post;
    }
}
