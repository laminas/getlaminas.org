<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;

class StaticPageHandler implements RequestHandlerInterface
{
    /** @var TemplateRendererInterface */
    private $renderer;

    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);

        return new HtmlResponse($this->renderer->render(
            $this->normalize($routeResult->getMatchedRouteName()),
            $request->getAttributes()
        ));
    }

    private function normalize(string $routeName) : string
    {
        return str_replace('.', '::', $routeName);
    }
}
