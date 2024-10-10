<?php

declare(strict_types=1);

namespace GetLaminas\Blog;

use JsonSerializable;
use Override;
use Psr\EventDispatcher\StoppableEventInterface;

class FetchBlogPostEvent implements
    JsonSerializable,
    StoppableEventInterface
{
    /** @var null|BlogPost */
    private $post;

    public function __construct(private readonly string $id)
    {
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id'   => $this->id,
            'post' => $this->post,
        ];
    }

    #[Override]
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
