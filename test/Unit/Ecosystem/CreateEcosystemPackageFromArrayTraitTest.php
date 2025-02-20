<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Ecosystem;

use DateTimeImmutable;
use Exception;
use GetLaminas\Ecosystem\CreateEcosystemPackageFromArrayTrait;
use GetLaminas\Ecosystem\EcosystemPackage;
use GetLaminas\Ecosystem\Enums\EcosystemCategoryEnum;
use GetLaminas\Ecosystem\Enums\EcosystemTypeEnum;
use GetLaminas\Ecosystem\Enums\EcosystemUsageEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CreateEcosystemPackageFromArrayTraitTest extends TestCase
{
    use CreateEcosystemPackageFromArrayTrait;

    public static function failingPackageDataProvider(): array
    {
        return [
            'invalidCategoryArray' => [[
                'category' => 'invalid category',
                'type'     => EcosystemTypeEnum::Library->value,
                'usage'    => EcosystemUsageEnum::Mezzio->value,
            ]],
            'invalidTypeArray' => [[
                'category' => EcosystemCategoryEnum::Integration->value,
                'type'     => 'invalid type',
                'usage'    => EcosystemUsageEnum::Mezzio->value,
            ]],
            'invalidUsageArray' => [[
                'category' => EcosystemCategoryEnum::Integration->value,
                'type'     => EcosystemTypeEnum::Library->value,
                'usage'    => 'invalid usage',
            ]],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('failingPackageDataProvider')]
    public function testWillNotCreateEcosystemPackageWithInvalidData(array $data): void
    {
        $this->assertNull($this->createEcosystemPackageFromArray($data));
    }

    /**
     * @throws Exception
     */
    public function testWillReturnValidObjectWithValidData(): void
    {
        $package = $this->createEcosystemPackageFromArray([
            'id' => "uniqueId",
            'name' => "vendorName/packageName",
            'type' => "library",
            'packagistUrl' => "packagistUrl",
            'repository' => "repositoryUrl",
            'description' => "",
            'usage' => "mezzio",
            'created' => 1736408707,
            'updated' => 1736408707,
            'category' => "tool",
            'stars' => 10,
            'issues' => 1,
            'downloads' => 100,
            'abandoned' => 0,
            'keywords' => ['user-defined', 'keywords'],
            'website' => 'user-defined website',
            'license' => 'MIT',
            'image' => ''
        ]);

        $this->assertInstanceOf(EcosystemPackage::class, $package);
        $this->assertInstanceOf(EcosystemCategoryEnum::class, $package->category);
        $this->assertInstanceOf(EcosystemTypeEnum::class, $package->type);
        $this->assertInstanceOf(EcosystemUsageEnum::class, $package->usage);
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
