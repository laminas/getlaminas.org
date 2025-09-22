<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Integration;

use Override;
use PDO;
use PHPUnit\Framework\TestCase;

class CommonTestCase extends TestCase
{
    protected string $testDb = ':memory:';

    protected ?PDO $pdo = null;

    #[Override]
    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite:' . $this->testDb);
    }
}
