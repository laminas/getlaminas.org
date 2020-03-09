<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace GetLaminas\Blog\Console;

use GetLaminas\Blog\BlogAuthor;
use GetLaminas\Blog\BlogPost;
use GetLaminas\Blog\Mapper\MapperInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Parser as YamlParser;
use Traversable;
use Laminas\Diactoros\Uri;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Laminas\Feed\Writer\Feed as FeedWriter;

use function file_exists;
use function file_put_contents;
use function method_exists;
use function sprintf;
use function str_replace;

class FeedGenerator extends Command
{
    use RoutesTrait;

    private $authorsPath;

    private $defaultAuthor = [
        'name'  => 'Matthew Weier O\'Phinney',
        'email' => 'me@mwop.net',
        'uri'   => 'https://mwop.net',
    ];

    private $io;

    private $mapper;

    private $renderer;

    private $router;

    public function __construct(
        MapperInterface $mapper,
        RouterInterface $router,
        TemplateRendererInterface $renderer,
        ServerUrlHelper $serverUrlHelper,
        string $authorsPath
    ) {
        $this->mapper      = $mapper;
        $this->router      = $this->seedRoutes($router);
        $this->renderer    = $renderer;
        $this->authorsPath = $authorsPath;

        if (method_exists($mapper, 'setAuthorDataRootPath')) {
            $mapper->setAuthorDataRootPath($authorsPath);
        }

        $serverUrlHelper->setUri(new Uri('https://getlaminas.org'));

        parent::__construct();
    }

    protected function configure() : void
    {
        $this->setName('blog:feed-generator');
        $this->setDescription('Generate blog feeds.');
        $this->setHelp('Generate feeds (RSS and Atom) for the blog, including all tags.');

        $this->addOption(
            'output-dir',
            'o',
            InputOption::VALUE_REQUIRED,
            'Directory to which to write the feeds.',
            'var/blog/feeds'
        );

        $this->addOption(
            'base-uri',
            'b',
            InputOption::VALUE_REQUIRED,
            'Base URI for the site.',
            'https://getlaminas.org'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io        = new SymfonyStyle($input, $output);
        $outputDir = $input->getOption('output-dir');
        $baseUri   = $input->getOption('base-uri');

        $io->title('Generating blog feeds');
        $io->text('<info>Generating base feeds</info>');
        $io->progressStart();

        $this->generateFeeds(
            $outputDir . '/',
            $baseUri,
            'Laminas Blog',
            'blog',
            'blog.feed',
            [],
            $this->mapper->fetchAll()
        );

        return 0;
    }

    private function generateFeeds(
        string $fileBase,
        string $baseUri,
        string $title,
        string $landingRoute,
        string $feedRoute,
        array $routeOptions,
        Traversable $posts
    ) {
        foreach (['atom', 'rss'] as $type) {
            $this->generateFeed($type, $fileBase, $baseUri, $title, $landingRoute, $feedRoute, $routeOptions, $posts);
        }
    }

    private function generateFeed(
        string $type,
        string $fileBase,
        string $baseUri,
        string $title,
        string $landingRoute,
        string $feedRoute,
        array $routeOptions,
        Traversable $posts
    ) {
        $routeOptions['type'] = $type;

        $landingUri = $baseUri . $this->generateUri($landingRoute, $routeOptions);
        $feedUri    = $baseUri . $this->generateUri($feedRoute, $routeOptions);

        $feed = new FeedWriter();
        $feed->setTitle($title);
        $feed->setLink($landingUri);
        $feed->setFeedLink($feedUri, $type);

        if ($type === 'rss') {
            $feed->setDescription($title);
        }

        $latest = false;

        if (method_exists($posts, 'setCurrentPageNumber')) {
            $posts->setCurrentPageNumber(1);
        }

        foreach ($posts as $post) {
            $html   = $post->body . $post->extended;
            $author = $this->getAuthor($post->author);

            if (! $latest) {
                $latest = $post;
            }

            $entry = $feed->createEntry();
            $entry->setTitle($post->title);
            // $entry->setLink($baseUri . $this->generateUri('blog.post', ['id' => $post->id]));
            $entry->setLink($baseUri . sprintf('/blog/%s.html', $post->id));

            $entry->addAuthor($author);
            $entry->setDateModified($post->updated);
            $entry->setDateCreated($post->created);
            $entry->setContent($this->createContent($html, $post));

            $feed->addEntry($entry);
        }

        // Set feed date
        $feed->setDateModified($latest->updated);

        // Write feed to file
        $file = sprintf('%s%s.xml', $fileBase, $type);
        $file = str_replace(' ', '+', $file);
        file_put_contents($file, $feed->export($type));
    }

    /**
     * Retrieve author metadata.
     *
     * @return string[]
     */
    private function getAuthor(BlogAuthor $author) : array
    {
        return [
            'name'  => $author->fullname ?: $author->username,
            'email' => $author->email,
            'uri'   => $author->url,
        ];
    }

    /**
     * Normalize generated URIs.
     *
     * @param string $route
     * @param array $options
     * @return string
     */
    private function generateUri(string $route, array $options) : string
    {
        $uri = $this->router->generateUri($route, $options);
        return str_replace('[/]', '', $uri);
    }

    /**
     * Modify this method to post-process content (e.g., to add an hcard)
     */
    private function createContent(string $content, BlogPost $post) : string
    {
        return $content;
    }
}
