<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use GetLaminas\Blog\Mapper\MapperInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Laminas\Paginator\Paginator;

class ListPostsHandler implements RequestHandlerInterface
{
    /** @var MapperInterface */
    private $mapper;

    /** @var RouterInterface */
    private $router;

    /** @var TemplateRendererInterface */
    private $template;

    public function __construct(
        MapperInterface $mapper,
        TemplateRendererInterface $template,
        RouterInterface $router
    ) {
        $this->mapper    = $mapper;
        $this->template  = $template;
        $this->router    = $router;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $tag   = str_replace(['+', '%20'], ' ', $request->getAttribute('tag', ''));
        $path  = $request->getAttribute('originalRequest', $request)->getUri()->getPath();
        $page  = $this->getPageFromRequest($request);
        $posts = $tag ? $this->mapper->fetchAllByTag($tag) : $this->mapper->fetchAll();

        $posts->setItemCountPerPage(10);

        // If the requested page is later than the last, redirect to the last
        if (count($posts) && $page > count($posts)) {
            return new RedirectResponse(sprintf('%s?page=%d', $path, count($posts)));
        }

        $posts->setCurrentPageNumber($page);

        return new HtmlResponse($this->template->render(
            'blog::list',
            $this->prepareView(
                $tag,
                iterator_to_array($posts->getItemsByPage($page)),
                $this->preparePagination($path, $page, $posts->getPages())
            )
        ));
    }

    private function getPageFromRequest(ServerRequestInterface $request) : int
    {
        $page = $request->getQueryParams()['page'] ?? 1;
        $page = (int) $page;
        return ($page < 1) ? 1 : $page;
    }

    /**
     * @var string $path
     * @var int $page
     * @var object $pagination
     * @return object $pagination
     */
    private function preparePagination(string $path, int $page, stdClass $pagination) : stdClass
    {
        $pagination->base_path = $path;
        $pagination->is_first  = ($page === $pagination->first);
        $pagination->is_last   = ($page === $pagination->last);

        $pages = [];
        for ($i = $pagination->firstPageInRange; $i <= $pagination->lastPageInRange; $i += 1) {
            $pages[] = [
                'base_path' => $path,
                'number'    => $i,
                'current'   => ($page === $i),
            ];
        }
        $pagination->pages = $pages;

        return $pagination;
    }

    /**
     * @param string $tag
     * @param BlogPost[] $entries
     * @param object $pagination
     * @return array
     */
    private function prepareView(string $tag, array $entries, stdClass $pagination) : array
    {
        $view = $tag ? ['tag' => $tag] : [];
        if ($tag) {
            $view['atom'] = $this->router->generateUri('blog.tag.feed', ['tag' => $tag, 'type' => 'atom']);
            $view['rss']  = $this->router->generateUri('blog.tag.feed', ['tag' => $tag, 'type' => 'rss']);
        }

        return array_merge($view, [
            'title'      => $tag ? 'Tag: ' . $tag : 'Blog Posts',
            'posts'      => $entries,
            'pagination' => $pagination,
        ]);
    }
}
