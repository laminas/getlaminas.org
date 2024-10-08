<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use GetLaminas\Blog\FetchBlogPostEvent;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Override;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DisplayPostHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly TemplateRendererInterface $template,
        private readonly RequestHandlerInterface $notFoundHandler,
    ) {
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id', false);

        if (! $id) {
            return $this->notFoundHandler->handle($request);
        }

        /** @var FetchBlogPostEvent $event */
        $event = $this->dispatcher->dispatch(new FetchBlogPostEvent($id));

        $post = $event->blogPost();

        if (! $post) {
            return $this->notFoundHandler->handle($request);
        }

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
