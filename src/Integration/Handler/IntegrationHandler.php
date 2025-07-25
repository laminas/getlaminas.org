<?php

declare(strict_types=1);

namespace GetLaminas\Integration\Handler;

use GetLaminas\Integration\Enums\IntegrationCategoryEnum;
use GetLaminas\Integration\Enums\IntegrationTypeEnum;
use GetLaminas\Integration\Integration;
use GetLaminas\Integration\Mapper\MapperInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Stdlib\ArrayUtils;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_map;
use function assert;
use function count;
use function implode;
use function is_array;
use function is_string;
use function max;
use function sprintf;
use function strtolower;

class IntegrationHandler implements RequestHandlerInterface
{
    public const string INTEGRATION_DIRECTORY = '/data/integration';
    public const string INTEGRATION_FILE      = 'integration-packages.json';

    public function __construct(
        private readonly MapperInterface $integrationMapper,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        $keywords = $queryParams['keywords'] ?? [];
        assert(is_array($keywords));
        $type = $queryParams['type'] ?? '';
        assert(is_string($type));
        $type     = IntegrationTypeEnum::tryFrom($type)?->name;
        $category = $queryParams['category'] ?? '';
        assert(is_string($category));
        $category = IntegrationCategoryEnum::tryFrom($category)?->name;
        $search   = $queryParams['q'] ?? '';
        assert(is_string($search));

        $packages = $this->integrationMapper->fetchAllByFilters(
            [
                'keywords' => $keywords !== []
                    ? array_map(
                        fn (string $keyword) => strtolower($keyword),
                        $keywords
                    ) : null,
                'type'     => [$type],
                'category' => [$category],
            ],
            $search
        );

        $path = $request->getAttribute('originalRequest', $request)->getUri()->getPath();
        assert(is_string($path));
        $page = $this->getPageFromRequest($request);
        $packages->setItemCountPerPage(9);

        // If the requested page is later than the last, redirect to the last
        // keep set keyword and search queries
        if (count($packages) && $page > count($packages)) {
            $keywordsQuery = '';
            if ($keywords !== []) {
                $keywordsQuery = '&keywords[]=' . implode("&keywords[]=", $keywords);
            }

            $searchQuery = '';
            if ($search !== '') {
                $searchQuery = '&q=' . strtolower($search);
            }

            $typeQuery = '';
            if ($type !== null) {
                $typeQuery = '&type=' . strtolower($type);
            }

            $categoryQuery = '';
            if ($category !== null) {
                $categoryQuery = '&category=' . strtolower($category);
            }

            return new RedirectResponse(
                sprintf(
                    '%s?page=%d%s%s%s%s',
                    $path,
                    count($packages),
                    $keywordsQuery,
                    $searchQuery,
                    $typeQuery,
                    $categoryQuery,
                )
            );
        }

        $packages->setCurrentPageNumber($page);

        return new HtmlResponse($this->renderer->render(
            'integration::list',
            $this->prepareView(
                $packages->getItemsByPage($page),
                $this->preparePagination($path, $page, $packages->getPages()),
                $keywords,
                $search,
                $type,
                $category
            ),
        ));
    }

    private function getPageFromRequest(ServerRequestInterface $request): int
    {
        $page = $request->getQueryParams()['page'] ?? 1;
        $page = (int) $page;
        return max($page, 1);
    }

    private function preparePagination(string $path, int $page, object $pagination): object
    {
        $pagination->base_path = $path;
        $pagination->is_first  = $page === $pagination->first;
        $pagination->is_last   = $page === $pagination->last;

        $pages = [];
        for ($i = (int) $pagination->firstPageInRange; $i <= (int) $pagination->lastPageInRange; $i += 1) {
            $pages[] = [
                'base_path' => $path,
                'number'    => $i,
                'current'   => $page === $i,
            ];
        }
        $pagination->pages = $pages;

        return $pagination;
    }

    /**
     * @param iterable<int, Integration> $entries
     * @psalm-return array<string, mixed>
     */
    private function prepareView(
        iterable $entries,
        object $pagination,
        array $keywords,
        string $search,
        ?string $typeQuery,
        ?string $categoryQuery
    ): array {
        return [
            ...[
                'integrations' => ArrayUtils::iteratorToArray($entries, false),
                'pagination'   => $pagination,
                'keywords'     => $keywords,
                'search'       => $search,
                'type'         => $typeQuery,
                'category'     => $categoryQuery,
            ],
        ];
    }
}
