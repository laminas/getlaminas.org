<?php

declare(strict_types=1);

namespace App\FrontMatter;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use Spatie\YamlFrontMatter\Document as SpatieDocument;

use function str_replace;
use function strpos;

final class Document implements DocumentInterface
{
    /** @var SpatieDocument */
    private $document;

    public function __construct(SpatieDocument $document)
    {
        $this->document = $document;
    }

    public function getYAML(): array
    {
        return $this->document->matter();
    }

    public function getContent(): string
    {
        $env = new Environment();
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new GithubFlavoredMarkdownExtension());
        $env->addExtension(new TableExtension());

        $converter = new MarkdownConverter($env);

        return $this->postProcessHtml(
            $converter->convertToHtml($this->document->body())->getContent()
        );
    }

    /**
     * Post-process HTML converted from markdown.
     */
    private function postProcessHtml(string $html): string
    {
        if (strpos($html, '<table>') !== false) {
            $html = str_replace('<table>', '<table class="table table-striped table-bordered table-hover">', $html);
        }

        return $html;
    }
}
