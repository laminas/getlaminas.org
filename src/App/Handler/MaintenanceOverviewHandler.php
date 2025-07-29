<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function in_array;
use function is_array;
use function is_string;
use function str_contains;

class MaintenanceOverviewHandler implements RequestHandlerInterface
{
    public const string CUSTOM_PROPERTIES_FILE      = 'maintenance-status.json';
    public const string CUSTOM_PROPERTIES_DIRECTORY = '/public/share';

    public const array MVC_COMPONENTS = [
        'laminas-developer-tools',
        'laminas-mvc',
        'laminas-mvc-console',
        'laminas-mvc-form',
        'laminas-mvc-plugin-fileprg',
        'laminas-mvc-plugin-flashmessenger',
        'laminas-mvc-plugin-identity',
        'laminas-mvc-plugin-prg',
        'laminas-mvc-plugins',
        'laminas-mvc-middleware',
        'laminas-mvc-i18n',
        'laminas-mvc-view',
        'laminas-test',
    ];

    public const array GITHUB_ORGANIZATIONS = [
        'mezzio'            => 'Mezzio',
        'laminas'           => 'Components',
        'laminas-mvc'       => 'MVC',
        'laminas-api-tools' => 'API Tools',
    ];

    /**
     * @param array<string, array<int, array<string, string|array<string, string>>>> $repositoryData
     */
    public function __construct(
        private array $repositoryData,
        private readonly string $lastUpdated,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        $search = $queryParams['q'] ?? null;
        assert(is_string($search) || $search === null);
        $status = $queryParams['status'] ?? null;
        assert(is_array($status) || $status === null);

        $this->filterData($search, $status);

        return new HtmlResponse($this->renderer->render(
            'app::maintenance-overview',
            [
                'repositoryData' => $this->repositoryData,
                'lastUpdated'    => $this->lastUpdated,
                'status'         => $status,
                'search'         => $search,
            ],
        ));
    }

    private function filterData(?string $search, ?array $status): void
    {
        foreach ($this->repositoryData as $name => $orgData) {
            foreach ($orgData as $key => $val) {
                assert(is_string($val['name']));
                assert(is_array($val['properties']));

                if ($search !== null && ! str_contains($val['name'], $search)) {
                    unset($this->repositoryData[$name][$key]);
                    continue;
                }

                if ($status !== null) {
                    /** @var array{property_name: string, value: string} $propertiesArray */
                    foreach ($val['properties'] as $propertiesArray) {
                        if (
                            isset($propertiesArray['property_name'])
                            && isset($propertiesArray['value'])
                            && $propertiesArray['property_name'] === 'maintenance-mode'
                        ) {
                            if (! in_array($propertiesArray['value'], $status)) {
                                unset($this->repositoryData[$name][$key]);
                            }
                        }
                    }
                }
            }
        }
    }
}
