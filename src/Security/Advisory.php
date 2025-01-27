<?php

declare(strict_types=1);

namespace GetLaminas\Security;

use App\AbstractCollection;
use Override;

class Advisory extends AbstractCollection
{
    protected const FOLDER_COLLECTION = 'data/advisories';
    protected const CACHE_FILE        = 'var/advisories.php';

    #[Override]
    protected function order(array $a, array $b): int
    {
        if ($b['date'] === $a['date']) {
            return $b['title'] <=> $a['title'];
        }
        return $b['date'] <=> $a['date']; // reverse order
    }
}
