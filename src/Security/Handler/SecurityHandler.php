<?php

declare(strict_types=1);

namespace GetLaminas\Security\Handler;

use DateTimeImmutable;
use GetLaminas\Security\Advisory;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Feed\Writer\Feed;
use Mezzio\Template;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_slice;
use function basename;
use function ceil;
use function count;
use function current;
use function preg_match;
use function sprintf;

class SecurityHandler implements RequestHandlerInterface
{
    private const ADVISORY_PER_PAGE = 10;
    private const ADVISORY_PER_FEED = 15;

    /** @var Advisory */
    private $advisory;

    /** @var Template\TemplateRendererInterface */
    private $template;

    public function __construct(Advisory $advisory, Template\TemplateRendererInterface $template)
    {
        $this->advisory = $advisory;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $action = $request->getAttribute('action', 'security');

        if ($action === 'feed') {
            return $this->feed($request);
        }

        $content = [];
        if ('advisories' === $action) {
            $params = $request->getQueryParams();
            $page   = isset($params['page']) ? (int) $params['page'] : 1;

            $allAdvisories = $this->advisory->getAll();
            $totPages      = (int) ceil(count($allAdvisories) / self::ADVISORY_PER_PAGE);

            if ((0 !== $totPages) && ($page > $totPages || $page < 1)) {
                return new HtmlResponse($this->template->render('error::404'));
            }

            $nextPage = $page === $totPages ? 0 : $page + 1;
            $prevPage = $page === 1 ? 0 : $page - 1;

            $advisories = array_slice($allAdvisories, ($page - 1) * self::ADVISORY_PER_PAGE, self::ADVISORY_PER_PAGE);
            $content    = [
                'advisories' => $advisories,
                'tot'        => $totPages,
                'page'       => $page,
                'prev'       => $prevPage,
                'next'       => $nextPage,
            ];
        }
        return new HtmlResponse($this->template->render(sprintf('security::%s', $action), $content));
    }

    protected function feed(ServerRequestInterface $request): ResponseInterface
    {
        $baseUrl     = (string) $request->getUri()->withPath('/security');
        $feedUrl     = (string) $request->getUri()->withQuery('')->withFragment('');
        $advisoryUrl = $request->getUri()->withPath('/security/advisory');

        $matches = [];
        preg_match('#(?P<type>atom|rss)#', $feedUrl, $matches);
        $feedType = $matches['type'] ?? 'rss';

        $feed = new Feed();
        $feed->setTitle('Laminas Project Security Advisories');
        $feed->setLink($baseUrl);
        $feed->setDescription('Reported and patched vulnerabilities in the Laminas Project');
        $feed->setFeedLink($feedUrl, $feedType);

        if ($feedType === 'rss') {
            $feed->addAuthor([
                'name'  => 'Laminas Project Security',
                'email' => 'security@getlaminas.org (Laminas Project Security)',
            ]);
        }

        $advisories = array_slice($this->advisory->getAll(), 0, self::ADVISORY_PER_FEED);
        $first      = current($advisories);
        $feed->setDateModified(new DateTimeImmutable($first['date']));

        foreach ($advisories as $id => $advisory) {
            $content = $this->advisory->getFromFile($id);
            $entry   = $feed->createEntry();
            $entry->setTitle($content['title']);
            $entry->setLink(sprintf('%s/%s', $advisoryUrl, basename($id, '.md')));
            $entry->addAuthor([
                'name'  => 'Laminas Project Security',
                'email' => 'security@getlaminas.org',
            ]);
            $entry->setDateCreated(new DateTimeImmutable($content['date']));
            $entry->setDateModified(new DateTimeImmutable($content['date']));
            $entry->setContent($content['body']);
            $feed->addEntry($entry);
        }

        $response = new TextResponse($feed->export($feedType));
        return $response->withHeader('Content-Type', sprintf('application/%s+xml', $feedType));
    }
}
