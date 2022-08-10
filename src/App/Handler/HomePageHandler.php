<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    private array $vendors;
    private TemplateRendererInterface $renderer;

    public function __construct(array $vendors, TemplateRendererInterface $renderer)
    {
        $this->vendors  = $vendors;
        $this->renderer = $renderer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->renderer->render(
            'app::home-page',
            ['commercialVendors' => $this->vendors],
        ));
    }
}
