<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Handler;

use GetLaminas\Ecosystem\EcosystemPackage;
use GetLaminas\Ecosystem\Enums\EcosystemCategoryEnum;
use GetLaminas\Ecosystem\Enums\EcosystemTypeEnum;
use GetLaminas\Ecosystem\Enums\EcosystemUsageEnum;
use GetLaminas\Ecosystem\Mapper\MapperInterface;
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
use function sprintf;
use function strtolower;

class EcosystemHandler implements RequestHandlerInterface
{
    public const string ECOSYSTEM_DIRECTORY = '/data/ecosystem';
    public const string ECOSYSTEM_FILE      = 'ecosystem-packages.json';

    public function __construct(
        private readonly MapperInterface $ecosystemMapper,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        $keywords = $queryParams['keywords'] ?? [];
        assert(is_array($keywords));
        $type     = $queryParams['type'] ?? '';
        $type     = EcosystemTypeEnum::tryFrom($type)?->name;
        $category = $queryParams['category'] ?? '';
        $category = EcosystemCategoryEnum::tryFrom($category)?->name;
        $usage    = $queryParams['usage'] ?? '';
        $usage    = EcosystemUsageEnum::tryFrom($usage)?->name;
        $search   = $queryParams['q'] ?? '';
        assert(is_string($search));

        $packages = $this->ecosystemMapper->fetchAllByFilters(
            [
                'keywords' => $keywords !== []
                    ? array_map(
                        fn (string $keyword) => strtolower($keyword),
                        $keywords
                    ) : null,
                'type'     => [$type],
                'category' => [$category],
                'usage'    => [$usage],
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
            if (! empty($keywords)) {
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

            $usageQuery = '';
            if ($usage !== null) {
                $usageQuery = '&usage=' . strtolower($usage);
            }

            return new RedirectResponse(
                sprintf(
                    '%s?page=%d%s%s%s%s%s',
                    $path,
                    count($packages),
                    $keywordsQuery,
                    $searchQuery,
                    $typeQuery,
                    $categoryQuery,
                    $usageQuery
                )
            );
        }

        $packages->setCurrentPageNumber($page);

        return new HtmlResponse($this->renderer->render(
            'ecosystem::list',
            $this->prepareView(
                $packages->getItemsByPage($page),
                $this->preparePagination($path, $page, $packages->getPages()),
                $keywords,
                $search,
                $type,
                $category,
                $usage
            ),
        ));
    }

    private function getPageFromRequest(ServerRequestInterface $request): int
    {
        $page = $request->getQueryParams()['page'] ?? 1;
        $page = (int) $page;
        return $page < 1 ? 1 : $page;
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
     * @param iterable<int, EcosystemPackage> $entries
     * @psalm-return array<string, mixed>
     */
    private function prepareView(
        iterable $entries,
        object $pagination,
        array $keywords,
        string $search,
        ?string $typeQuery,
        ?string $categoryQuery,
        ?string $usageQuery
    ): array {
        return [
            ...[
                'ecosystemPackages' => ArrayUtils::iteratorToArray($entries, false),
                'pagination'        => $pagination,
                'keywords'          => $keywords,
                'search'            => $search,
                'type'              => $typeQuery,
                'category'          => $categoryQuery,
                'usage'             => $usageQuery,
            ],
        ];
    }
}
