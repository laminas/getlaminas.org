<?php

namespace GetLaminas\Security;

use App\AbstractCollection;

class Advisory extends AbstractCollection
{
    protected const FOLDER_COLLECTION = 'data/advisories';
    protected const CACHE_FILE        = 'var/advisories.php';

    protected function order($a, $b)
    {
        if ($b['date'] === $a['date']) {
            return $b['title'] <=> $a['title'];
        }
        return $b['date'] <=> $a['date']; // reverse order
    }
}
