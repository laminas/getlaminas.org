<?php

namespace GetLaminas\Security\Handler;

use GetLaminas\Security\Advisory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class AdvisoryHandler implements RequestHandlerInterface
{
    /** @var Advisory */
    private $advisory;

    /** @var Template\TemplateRendererInterface */
    private $template;

    public function __construct(Advisory $advisory, Template\TemplateRendererInterface $template)
    {
        $this->advisory = $advisory;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $advisory = $request->getAttribute('advisory', false);
        $file = sprintf('data/advisories/%s.md', basename($advisory));
        if (! $advisory || ! file_exists($file)) {
            return new HtmlResponse($this->template->render('error::404'));
        }
        $content = $this->advisory->getFromFile($file);
        $content['layout'] = 'layout::default';
        $content['advisory'] = $advisory;

        return new HtmlResponse($this->template->render('security::advisory', $content));
    }
}
