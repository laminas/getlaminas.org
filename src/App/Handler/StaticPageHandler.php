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

use function assert;
use function is_string;
use function str_replace;

final class StaticPageHandler implements RequestHandlerInterface
{
    public function __construct(private readonly TemplateRendererInterface $renderer)
    {
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);

        $routeName = $routeResult->getMatchedRouteName();
        assert(is_string($routeName));

        /** @var array<non-empty-string, mixed> $attributes */
        $attributes = $request->getAttributes();

        return new HtmlResponse($this->renderer->render(
            $this->normalize($routeName),
            $attributes
        ));
    }

    /** @return non-empty-string */
    private function normalize(string $routeName): string
    {
        $template = str_replace('.', '::', $routeName);
        assert($template !== '');

        return $template;
    }
}
