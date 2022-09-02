<?php

declare(strict_types=1);

namespace App\ContentParser;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Query;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;

use function file_get_contents;

final class Parser implements ParserInterface
{
    private readonly MarkdownConverter $converter;

    private readonly MarkdownParser $parser;

    private readonly HtmlRenderer $renderer;

    public function __construct()
    {
        // Create default converter
        $environment = new Environment(
            [
                'default_attributes' => [
                    Table::class => [
                        'class' => 'table table-striped table-bordered table-hover',
                    ],
                ],
                'heading_permalink'  => [
                    'insert'            => 'after',
                    'min_heading_level' => 1,
                    'max_heading_level' => 2,
                ],
            ]
        );
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DefaultAttributesExtension());
        $environment->addExtension(new FrontMatterExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableExtension());

        $this->converter = new MarkdownConverter($environment);

        // Create parser and renderer for table of contents
        $environment = new Environment(
            [
                'table_of_contents' => [
                    'min_heading_level' => 2,
                    'max_heading_level' => 2,
                ],
            ]
        );
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FrontMatterExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableOfContentsExtension());

        $this->parser   = new MarkdownParser($environment);
        $this->renderer = new HtmlRenderer($environment);
    }

    public function parse(string $file): DocumentInterface
    {
        $markdown = file_get_contents($file);

        // Render markdown content
        /** @var RenderedContentWithFrontMatter $renderedContent */
        $renderedContent = $this->converter->convert($markdown);

        // Extract table of contents
        $document = $this->parser->parse($markdown);
        $node     = (new Query())
            ->where(Query::type(TableOfContents::class))
            ->findOne($document);

        $tableOfContents = null;
        if ($node) {
            $tableOfContents = $this->renderer->renderNodes([$node]);
        }

        return new Document($renderedContent, $tableOfContents);
    }
}
