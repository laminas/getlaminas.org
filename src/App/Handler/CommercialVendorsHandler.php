<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CommercialVendorsHandler implements RequestHandlerInterface
{
    public function __construct(private readonly array $vendors, private readonly TemplateRendererInterface $renderer)
    {
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->renderer->render(
            'app::commercial-vendor-program',
            ['commercialVendors' => $this->vendors],
        ));
    }
}
