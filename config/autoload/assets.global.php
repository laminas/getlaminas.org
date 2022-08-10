<?php

declare(strict_types=1);

$assetRevisionsFile = getcwd() . '/data/assets.json';
if (! file_exists($assetRevisionsFile)) {
    return [];
}

$revisions = json_decode(file_get_contents($assetRevisionsFile), true);

if (! is_array($revisions)) {
    return [];
}

return ['asset-revisions' => $revisions];
