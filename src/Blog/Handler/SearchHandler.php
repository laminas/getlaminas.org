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
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Helper\UrlHelper;

class SearchHandler implements RequestHandlerInterface
{
    /** @var MapperInterface */
    private $mapper;

    /** @var UrlHelper */
    private $urlHelper;

    public function __construct(MapperInterface $mapper, UrlHelper $urlHelper)
    {
        $this->mapper    = $mapper;
        $this->urlHelper = $urlHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $toMatch = $request->getQueryParams()['q'] ?? '';

        if ('' === $toMatch) {
            return new JsonResponse([]);
        }

        $results = array_map(function ($row) {
            return [
                'link'  => $this->urlHelper->generate('blog.post', ['id' => $row['id']]),
                'title' => $row['title'],
            ];
        }, $this->mapper->search($toMatch));

        return new JsonResponse($results);
    }
}
