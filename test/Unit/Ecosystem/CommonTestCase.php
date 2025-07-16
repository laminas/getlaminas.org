<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Ecosystem;

use PDO;
use PHPUnit\Framework\TestCase;

class CommonTestCase extends TestCase
{
    protected string $testDb = ':memory:';

    protected ?PDO $pdo = null;

    protected function setup(): void
    {
        $this->pdo = new PDO('sqlite:' . $this->testDb);
    }
}
