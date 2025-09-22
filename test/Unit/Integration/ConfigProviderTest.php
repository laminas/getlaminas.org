<?php

declare(strict_types=1);

namespace Unit\Integration;

use GetLaminas\Integration\ConfigProvider;
use GetLaminas\Integration\Console\CreateIntegrationDatabase;
use GetLaminas\Integration\Console\SeedIntegrationDatabase;
use GetLaminas\Integration\Handler\IntegrationHandler;
use GetLaminas\Integration\Mapper\PdoMapper;
use Override;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    protected array $config = [];

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->config = (new ConfigProvider())();
    }

    public function testConfigHasKeys(): void
    {
        $this->assertArrayHasKey('integration', $this->config);
        $this->assertArrayHasKey('dependencies', $this->config);
        $this->assertArrayHasKey('laminas-cli', $this->config);
        $this->assertArrayHasKey('templates', $this->config);
    }

    public function testIntegrationHasDb(): void
    {
        $this->assertArrayHasKey('db', $this->config['integration']);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['factories']);
        $this->assertArrayHasKey('config-packages', $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(IntegrationHandler::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(PdoMapper::class, $this->config['dependencies']['factories']);
    }

    public function testDependenciesHasDelegators(): void
    {
        $this->assertArrayHasKey('delegators', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['delegators']);
        $this->assertArrayHasKey(CreateIntegrationDatabase::class, $this->config['dependencies']['delegators']);
        $this->assertArrayHasKey(SeedIntegrationDatabase::class, $this->config['dependencies']['delegators']);
    }

    public function testDependenciesHasInvokables(): void
    {
        $this->assertArrayHasKey('invokables', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['invokables']);
        $this->assertArrayHasKey(SeedIntegrationDatabase::class, $this->config['dependencies']['invokables']);
        $this->assertArrayHasKey(CreateIntegrationDatabase::class, $this->config['dependencies']['invokables']);
    }

    public function testCommandsAreRegistered(): void
    {
        $this->assertArrayHasKey('commands', $this->config['laminas-cli']);
        $this->assertContains(
            SeedIntegrationDatabase::class,
            $this->config['laminas-cli']['commands']
        );
        $this->assertContains(
            CreateIntegrationDatabase::class,
            $this->config['laminas-cli']['commands']
        );
    }

    public function testGetTemplates(): void
    {
        $this->assertArrayHasKey('paths', $this->config['templates']);
        $this->assertIsArray($this->config['templates']['paths']);
        $this->assertArrayHasKey('integration', $this->config['templates']['paths']);
        $this->assertDirectoryExists($this->config['templates']['paths']['integration'][0]);
    }
}
