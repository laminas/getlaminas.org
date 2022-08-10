<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use GetLaminas\Blog\FetchBlogPostEvent;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DisplayPostHandler implements RequestHandlerInterface
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var RequestHandlerInterface */
    private $notFoundHandler;

    /** @var TemplateRendererInterface */
    private $template;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TemplateRendererInterface $template,
        RequestHandlerInterface $notFoundHandler
    ) {
        $this->dispatcher      = $dispatcher;
        $this->template        = $template;
        $this->notFoundHandler = $notFoundHandler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id', false);

        if (! $id) {
            return $this->notFoundHandler->handle($request);
        }

        // @var \GetLaminas\Blog\FetchBlogPostEvent $event
        $event = $this->dispatcher->dispatch(new FetchBlogPostEvent($id));

        // @var null|\GetLaminas\Blog\BlogPost $post
        $post = $event->blogPost();

        if (! $post) {
            return $this->notFoundHandler->handle($request);
        }

        // @var \DateTimeInterface $lastModified
        $lastModified = $post->updated ?: $post->created;

        return new HtmlResponse(
            $this->template->render('blog::post', [
                'post' => $post,
            ]),
            200,
            [
                'Last-Modified' => $lastModified->format('r'),
            ]
        );
    }
}
