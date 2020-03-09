<?php

declare(strict_types=1);

namespace GetLaminas\Blog;

class BlogAuthor
{
    /** @var string */
    public $email;

    /** @var string */
    public $fullname;

    /** @var string */
    public $url;

    /** @var string */
    public $username;

    public function __construct(
        string $username,
        string $fullname,
        string $email,
        string $url
    ) {
        $this->username = $username;
        $this->fullname = $fullname;
        $this->email    = $email;
        $this->url      = $url;
    }
}
