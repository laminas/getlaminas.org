<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use GetLaminas\Blog\Mapper\MapperInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Helper\UrlHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_map;

class SearchHandler implements RequestHandlerInterface
{
    public function __construct(
        private MapperInterface $mapper,
        private UrlHelper $urlHelper,
    ): void {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $toMatch = $request->getQueryParams()['q'] ?? '';

        if (! is_string($toMatch) || '' === $toMatch) {
            return new JsonResponse([]);
        }

        $results = array_map(function (array $row): array {
            return [
                'link'  => $this->urlHelper->generate('blog.post', ['id' => $row['id']]),
                'title' => $row['title'],
            ];
        }, $this->mapper->search($toMatch));

        return new JsonResponse($results);
    }
}
