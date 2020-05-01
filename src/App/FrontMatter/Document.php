<?php

declare(strict_types=1);

namespace App\FrontMatter;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Table\TableExtension;
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
        $env = Environment::createCommonMarkEnvironment();
        $env->addExtension(new TableExtension());

        $converter = new CommonMarkConverter([], $env);

        return $this->postProcessHtml(
            $converter->convertToHtml($this->document->body())
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
