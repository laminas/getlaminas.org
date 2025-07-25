<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Integration\Console;

use GetLaminas\Integration\Console\CreateIntegrationDatabase;
use LaminasTest\Unit\Integration\CommonTestCase;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(CreateIntegrationDatabase::class)]
class CreateIntegrationDatabaseTest extends CommonTestCase
{
    private CreateIntegrationDatabase|MockObject $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->createMock(CreateIntegrationDatabase::class);
    }

    public function testWillCreateDatabase(): void
    {
        $this->assertInstanceOf(PDO::class, $this->subject->createDatabase(
            'sqlite:' . $this->testDb
        ));
    }
}
