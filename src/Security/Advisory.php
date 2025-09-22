<?php

declare(strict_types=1);

namespace GetLaminas\Security;

use App\AbstractCollection;
use Override;

final class Advisory extends AbstractCollection
{
    /** @var string */
    protected const string FOLDER_COLLECTION = 'data/advisories';
    /** @var string */
    protected const string CACHE_FILE = 'var/advisories.php';

    #[Override]
    protected function order(array $a, array $b): int
    {
        if ($b['date'] === $a['date']) {
            return $b['title'] <=> $a['title'];
        }
        return $b['date'] <=> $a['date']; // reverse order
    }
}
