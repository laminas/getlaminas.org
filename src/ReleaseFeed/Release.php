<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use DateTimeInterface;

class Release
{
    public $author;
    public $content;
    public $date;
    public $package;
    public $url;
    public $version;

    public function __construct(
        string $package,
        string $version,
        string $url,
        string $content,
        DateTimeInterface $date,
        Author $author
    ) {
        $this->package = $package;
        $this->version = $version;
        $this->url     = $url;
        $this->content = $content;
        $this->date    = $date;
        $this->author  = $author;
    }
}
