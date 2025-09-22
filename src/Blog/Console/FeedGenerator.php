<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Console;

use GetLaminas\Blog\BlogAuthor;
use GetLaminas\Blog\BlogPost;
use GetLaminas\Blog\Mapper\MapperInterface;
use Laminas\Diactoros\Uri;
use Laminas\Feed\Writer\Feed as FeedWriter;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Override;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Traversable;

use function file_put_contents;
use function method_exists;
use function sprintf;
use function str_replace;

final class FeedGenerator extends Command
{
    use RoutesTrait;

    private array $defaultAuthor = [
        'name'  => 'Matthew Weier O\'Phinney',
        'email' => 'me@mwop.net',
        'uri'   => 'https://mwop.net',
    ];

    public function __construct(
        private MapperInterface $mapper,
        private RouterInterface $router,
        private TemplateRendererInterface $renderer,
        ServerUrlHelper $serverUrlHelper,
        private string $authorsPath
    ) {
        $this->seedRoutes($router);

        if (method_exists($mapper, 'setAuthorDataRootPath')) {
            $mapper->setAuthorDataRootPath($authorsPath);
        }

        $serverUrlHelper->setUri(new Uri('https://getlaminas.org'));

        parent::__construct();
    }

    #[Override]
    protected function configure(): void
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

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
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
    ): void {
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
    ): void {
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
            /** @var BlogPost $post */
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
        if ($latest instanceof BlogPost) {
            $feed->setDateModified($latest->updated);
        }

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
    private function getAuthor(BlogAuthor $author): array
    {
        return [
            'name'  => $author->fullName ?: $author->username,
            'email' => $author->email,
            'uri'   => $author->url,
        ];
    }

    /**
     * Normalize generated URIs.
     *
     * @param array $options
     */
    private function generateUri(string $route, array $options): string
    {
        $uri = $this->router->generateUri($route, $options);
        return str_replace('[/]', '', $uri);
    }

    /**
     * Modify this method to post-process content (e.g., to add an hcard)
     */
    private function createContent(string $content, BlogPost $post): string
    {
        return $content;
    }
}
