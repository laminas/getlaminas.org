<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Console;

use GetLaminas\Blog\CreateBlogPostFromDataArray;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function file_put_contents;
use function getcwd;
use function implode;
use function json_encode;
use function realpath;
use function sprintf;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class GenerateSearchData extends Command
{
    use CreateBlogPostFromDataArray;

    protected function configure(): void
    {
        $this->setName('blog:generate-search-data');
        $this->setDescription('Generate site search data.');
        $this->setHelp('Generate site search data based on blog posts.');

        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_REQUIRED,
            'Base path of the application; posts are expected at $path/data/blog/ '
            . 'and search terms will be written to $path/public/js/search_terms.json',
            realpath(getcwd())
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $basePath = $input->getOption('path');
        $path     = realpath($basePath) . '/data/blog';

        $io->title('Generating search metadata');

        $documents = [];
        foreach (new MarkdownFileFilter($path) as $fileInfo) {
            $post        = $this->createBlogPostFromDataArray(['path' => $fileInfo->getPathname()]);
            $documents[] = [
                'id'      => sprintf('/blog/%s.html', $post->id),
                'tags'    => implode(' ', $post->tags),
                'title'   => $post->title,
                'content' => $post->body . $post->extended,
            ];
        }

        file_put_contents(
            realpath($basePath) . '/public/js/search_terms.json',
            json_encode(['docs' => $documents], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        $io->success('Generated search metadata');

        return 0;
    }
}
