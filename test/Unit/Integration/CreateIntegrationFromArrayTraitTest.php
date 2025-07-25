<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Integration;

use DateTimeImmutable;
use Exception;
use GetLaminas\Integration\CreateIntegrationFromArrayTrait;
use GetLaminas\Integration\Enums\IntegrationCategoryEnum;
use GetLaminas\Integration\Enums\IntegrationTypeEnum;
use GetLaminas\Integration\Integration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CreateIntegrationFromArrayTraitTest extends TestCase
{
    use CreateIntegrationFromArrayTrait;

    public static function failingPackageDataProvider(): array
    {
        return [
            'invalidCategoryArray' => [
                [
                    'category' => 'invalid category',
                    'type'     => IntegrationTypeEnum::Library->value,
                ],
            ],
            'invalidTypeArray'     => [
                [
                    'category' => IntegrationCategoryEnum::Integration->value,
                    'type'     => 'invalid type',
                ],
            ],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('failingPackageDataProvider')]
    public function testWillNotCreateIntegrationWithInvalidData(array $data): void
    {
        $this->assertNull($this->createIntegrationFromArray($data));
    }

    /**
     * @throws Exception
     */
    public function testWillReturnValidObjectWithValidData(): void
    {
        $package = $this->createIntegrationFromArray([
            'id'           => "uniqueId",
            'name'         => "vendorName/packageName",
            'type'         => "library",
            'packagistUrl' => "packagistUrl",
            'repository'   => "repositoryUrl",
            'description'  => "",
            'created'      => 1736408707,
            'updated'      => 1736408707,
            'category'     => "tool",
            'stars'        => 10,
            'issues'       => 1,
            'downloads'    => 100,
            'abandoned'    => 0,
            'keywords'     => ['user-defined', 'keywords'],
            'website'      => 'user-defined website',
            'license'      => 'MIT',
            'image'        => '',
        ]);

        $this->assertInstanceOf(Integration::class, $package);
        $this->assertInstanceOf(IntegrationCategoryEnum::class, $package->category);
        $this->assertInstanceOf(IntegrationTypeEnum::class, $package->type);
    }

    /**
     * @throws Exception
     */
    public function testWillCreateDateTime(): void
    {
        $this->assertEquals(
            new DateTimeImmutable('01-01-2025'),
            $this->createDateTimeFromString('01-01-2025')
        );
    }
}
