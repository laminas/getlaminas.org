<?php

declare(strict_types=1);

namespace GetLaminas\Blog;

use DateTimeInterface;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use League\Plates\Template\Template;
use Override;
use Psr\Container\ContainerInterface;

use function sprintf;

class PlatesFunctionsDelegator implements ExtensionInterface
{
    public ?Template $template = null;

    public function __invoke(ContainerInterface $container, string $name, callable $factory): Engine
    {
        /** @var Engine $engine */
        $engine = $factory();
        $engine->loadExtension($this);
        return $engine;
    }

    #[Override]
    public function register(Engine $engine): void
    {
        $engine->registerFunction('formatDate', [$this, 'formatDate']);
        $engine->registerFunction('formatDateRfc', [$this, 'formatDateRfc']);
        $engine->registerFunction('postAuthor', [$this, 'postAuthor']);
        $engine->registerFunction('postUrl', [$this, 'postUrl']);
    }

    public function formatDate(DateTimeInterface $date, string $format = 'j F Y'): string
    {
        return $date->format($format);
    }

    public function formatDateRfc(DateTimeInterface $date): string
    {
        return $this->formatDate($date, 'c');
    }

    public function postAuthor(BlogPost $post): string
    {
        $author = $post->author;
        if ($author->url === '') {
            return $author->fullname ?: $author->username;
        }

        return sprintf('<a href="%s" target="_blank">%s</a>', $author->url, $author->fullname ?: $author->username);
    }

    public function postUrl(BlogPost $post): string
    {
        if (null === $this->template) {
            return '';
        }

        return $this->template->url('blog.post', ['id' => $post->id]);
    }
}
