<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function str_replace;

class StaticPageHandler implements RequestHandlerInterface
{
    public function __construct(private readonly TemplateRendererInterface $renderer)
    {
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);

        return new HtmlResponse($this->renderer->render(
            $this->normalize($routeResult->getMatchedRouteName()),
            $request->getAttributes()
        ));
    }

    private function normalize(string $routeName): string
    {
        return str_replace('.', '::', $routeName);
    }
}
