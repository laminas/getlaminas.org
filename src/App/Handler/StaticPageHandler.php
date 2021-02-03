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
            array_merge(
                $request->getAttributes(),
                [
                    'commercialVendors' => [
                        [
                            'name' => 'Roave',
                            'logo' => 'http://ocramius.github.io/zf2-dpc-tutorial-slides/images/Roave_Logo_-_720p.png', // @todo don't hotlink
                            'url' => 'https://roave.com',
                            'text' => 'Roave is a full-service web development firm, offering services such as consulting, training, software development, and more.',
                        ],
                        [
                            'name' => 'Roave',
                            'logo' => 'http://ocramius.github.io/zf2-dpc-tutorial-slides/images/Roave_Logo_-_720p.png', // @todo don't hotlink
                            'url' => 'https://roave.com',
                            'text' => 'Roave is a full-service web development firm, offering services such as consulting, training, software development, and more.',
                        ],
                        [
                            'name' => 'Roave',
                            'logo' => 'http://ocramius.github.io/zf2-dpc-tutorial-slides/images/Roave_Logo_-_720p.png', // @todo don't hotlink
                            'url' => 'https://roave.com',
                            'text' => 'Roave is a full-service web development firm, offering services such as consulting, training, software development, and more.',
                        ],
                        [
                            'name' => 'Roave',
                            'logo' => 'http://ocramius.github.io/zf2-dpc-tutorial-slides/images/Roave_Logo_-_720p.png', // @todo don't hotlink
                            'url' => 'https://roave.com',
                            'text' => 'Roave is a full-service web development firm, offering services such as consulting, training, software development, and more.',
                        ],
                        [
                            'name' => 'Roave',
                            'logo' => 'http://ocramius.github.io/zf2-dpc-tutorial-slides/images/Roave_Logo_-_720p.png', // @todo don't hotlink
                            'url' => 'https://roave.com',
                            'text' => 'Roave is a full-service web development firm, offering services such as consulting, training, software development, and more.',
                        ],
                        [
                            'name' => 'Roave',
                            'logo' => 'http://ocramius.github.io/zf2-dpc-tutorial-slides/images/Roave_Logo_-_720p.png', // @todo don't hotlink
                            'url' => 'https://roave.com',
                            'text' => 'Roave is a full-service web development firm, offering services such as consulting, training, software development, and more.',
                        ],
                        [
                            'name' => 'Roave',
                            'logo' => 'http://ocramius.github.io/zf2-dpc-tutorial-slides/images/Roave_Logo_-_720p.png', // @todo don't hotlink
                            'url' => 'https://roave.com',
                            'text' => 'Roave is a full-service web development firm, offering services such as consulting, training, software development, and more.',
                        ],
                    ],
                ]
            )
        ));
    }

    private function normalize(string $routeName): string
    {
        return str_replace('.', '::', $routeName);
    }
}
