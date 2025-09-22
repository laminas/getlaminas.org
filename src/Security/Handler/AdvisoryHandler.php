<?php

declare(strict_types=1);

namespace GetLaminas\Security\Handler;

use GetLaminas\Security\Advisory;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function basename;
use function file_exists;
use function sprintf;

final class AdvisoryHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Advisory $advisory,
        private readonly Template\TemplateRendererInterface $template
    ) {
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $advisory = $request->getAttribute('advisory', false);
        if (! $advisory) {
            return new HtmlResponse($this->template->render('error::404'));
        }

        $file = sprintf('data/advisories/%s.md', basename($advisory));
        if (! file_exists($file)) {
            return new HtmlResponse($this->template->render('error::404'));
        }

        $content             = $this->advisory->getFromFile($file);
        $content['layout']   = 'layout::default';
        $content['advisory'] = $advisory;

        return new HtmlResponse($this->template->render('security::advisory', $content));
    }
}
