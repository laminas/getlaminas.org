<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use GetLaminas\Blog\BlogPost;
use GetLaminas\Blog\Mapper\MapperInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Stdlib\ArrayUtils;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function count;
use function sprintf;
use function str_replace;

class ListPostsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly MapperInterface $mapper,
        private readonly TemplateRendererInterface $template,
        private readonly RouterInterface $router
    ) {
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tag   = str_replace(['+', '%20'], ' ', $request->getAttribute('tag', ''));
        $path  = $request->getAttribute('originalRequest', $request)->getUri()->getPath();
        $page  = $this->getPageFromRequest($request);
        $posts = $tag ? $this->mapper->fetchAllByTag($tag) : $this->mapper->fetchAll();

        $posts->setItemCountPerPage(9);

        // If the requested page is later than the last, redirect to the last
        if (count($posts) && $page > count($posts)) {
            return new RedirectResponse(sprintf('%s?page=%d', $path, count($posts)));
        }

        $posts->setCurrentPageNumber($page);

        return new HtmlResponse($this->template->render(
            'blog::list',
            $this->prepareView(
                $tag,
                $posts->getItemsByPage($page),
                $this->preparePagination($path, $page, $posts->getPages())
            )
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
     * @param iterable<int, BlogPost> $entries
     * @psalm-return array<string, mixed>
     */
    private function prepareView(string $tag, iterable $entries, object $pagination): array
    {
        $view = $tag ? ['tag' => $tag] : [];
        if ($tag) {
            $view['atom'] = $this->router->generateUri('blog.tag.feed', ['tag' => $tag, 'type' => 'atom']);
            $view['rss']  = $this->router->generateUri('blog.tag.feed', ['tag' => $tag, 'type' => 'rss']);
        }

        return [
            ...$view,
            ...[
                'posts'      => ArrayUtils::iteratorToArray($entries, false),
                'pagination' => $pagination,
            ],
        ];
    }
}
