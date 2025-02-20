<?php

declare(strict_types=1);

namespace GetLaminas\Blog;

use DateTimeInterface;

class BlogPost
{
    /** @var BlogAuthor */
    public $author;

    /** @var string */
    public $body;

    /** @var DateTimeInterface */
    public $created;

    /** @var string */
    public $extended;

    public ?string $toc;

    /** @var string */
    public $id;

    /** @var bool */
    public $isDraft;

    /** @var bool */
    public $isPublic;

    /** @var string[] */
    public $tags;

    /** @var string */
    public $title;

    /** @var null|DateTimeInterface */
    public $updated;

    public function __construct(
        string $id,
        string $title,
        BlogAuthor $author,
        DateTimeInterface $created,
        ?DateTimeInterface $updated,
        array $tags,
        string $body,
        string $extended,
        ?string $toc,
        bool $isDraft,
        bool $isPublic
    ) {
        $this->id       = $id;
        $this->title    = $title;
        $this->author   = $author;
        $this->created  = $created;
        $this->updated  = $updated;
        $this->tags     = $tags;
        $this->body     = $body;
        $this->extended = $extended;
        $this->toc      = $toc;
        $this->isDraft  = $isDraft;
        $this->isPublic = $isPublic;
    }
}
