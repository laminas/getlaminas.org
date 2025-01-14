<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Ecosystem\Console;

use GetLaminas\Ecosystem\Console\CreateEcosystemDatabase;
use LaminasTest\Unit\Ecosystem\CommonTestCase;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(CreateEcosystemDatabase::class)]
class CreateEcosystemDatabaseTest extends CommonTestCase
{
    private CreateEcosystemDatabase|MockObject $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->createMock(CreateEcosystemDatabase::class);
    }

    public function testWillCreateDatabase(): void
    {
        $this->assertInstanceOf(PDO::class, $this->subject->createDatabase(
            'sqlite:' . $this->testDb
        ));
    }
}
