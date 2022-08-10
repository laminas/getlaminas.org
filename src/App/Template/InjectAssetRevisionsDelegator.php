<?php

declare(strict_types=1);

namespace App\Template;

use Closure;
use League\Plates\Engine as PlatesEngine;
use Psr\Container\ContainerInterface;

use function assert;
use function file_exists;
use function file_get_contents;
use function getcwd;
use function is_array;
use function json_decode;

class InjectAssetRevisionsDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory): PlatesEngine
    {
        $engine = $factory();
        assert($engine instanceof PlatesEngine);

        $config = $container->get('config') ?? [];
        assert(is_array($config));

        $revisions = $config['asset-revisions'] ?? [];
        assert(is_array($revisions));

        isset($config['debug'])
            ? $this->injectAssets($engine, Closure::fromCallable([$this, 'getAssetMap']))
            : $this->injectAssets($engine, function () use ($revisions): array {
                return $revisions;
            });

        return $engine;
    }

    private function injectAssets(PlatesEngine $engine, callable $getAssetMap): void
    {
        $engine->registerFunction('assets', function (string $asset) use ($getAssetMap) {
            $assetMap = $getAssetMap();
            assert(is_array($assetMap));

            return $assetMap[$asset] ?? $asset;
        });
    }

    private function getAssetMap(): array
    {
        $assetRevisionsFile = getcwd() . '/data/assets.json';
        if (! file_exists($assetRevisionsFile)) {
            return [];
        }

        $revisions = json_decode(file_get_contents($assetRevisionsFile), true);

        return is_array($revisions) ? $revisions : [];
    }
}
