<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MaintenanceOverviewHandler implements RequestHandlerInterface
{
    public const string CUSTOM_PROPERTIES_FILE      = 'maintenance-status.json';
    public const string CUSTOM_PROPERTIES_DIRECTORY = '/public/share';

    public const array MVC_COMPONENTS = [
        'laminas-mvc',
        'laminas-modulemanager',
        'laminas-mvc-plugin-fileprg',
        'laminas-mvc-plugin-flashmessenger',
        'laminas-mvc-plugin-identity',
        'laminas-mvc-plugin-prg',
        'laminas-mvc-middleware',
        'laminas-mvc-i18n',
        'laminas-psr7bridge',
        'laminas-mvc-console',
        'laminas-test',
    ];

    public function __construct(
        private array $repositoryData,
        private string $lastUpdated,
        private TemplateRendererInterface $renderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->renderer->render(
            'app::maintenance-overview',
            [
                'repositoryData' => $this->repositoryData,
                'lastUpdated'    => $this->lastUpdated,
            ],
        ));
    }
}
