<?php

declare(strict_types=1);

namespace Unit\Ecosystem;

use GetLaminas\Ecosystem\ConfigProvider;
use GetLaminas\Ecosystem\Console\CreateEcosystemDatabase;
use GetLaminas\Ecosystem\Console\SeedEcosystemDatabase;
use GetLaminas\Ecosystem\Handler\EcosystemHandler;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ConfigProviderTest extends TestCase
{
    protected array $config = [];

    protected function setup(): void
    {
        parent::setUp();

        $this->config = (new ConfigProvider())();
    }

    public function testConfigHasKeys(): void
    {
        $this->assertArrayHasKey('ecosystem', $this->config);
        $this->assertArrayHasKey('dependencies', $this->config);
        $this->assertArrayHasKey('laminas-cli', $this->config);
        $this->assertArrayHasKey('templates', $this->config);
    }

    public function testEcosystemHasDb(): void
    {
        $this->assertArrayHasKey('db', $this->config['ecosystem']);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['factories']);
        $this->assertArrayHasKey('config-packages', $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(EcosystemHandler::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(PdoMapper::class, $this->config['dependencies']['factories']);
    }

    public function testDependenciesHasDelegators(): void
    {
        $this->assertArrayHasKey('delegators', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['delegators']);
        $this->assertArrayHasKey(CreateEcosystemDatabase::class, $this->config['dependencies']['delegators']);
        $this->assertArrayHasKey(SeedEcosystemDatabase::class, $this->config['dependencies']['delegators']);
    }

    public function testDependenciesHasInvokables(): void
    {
        $this->assertArrayHasKey('invokables', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['invokables']);
        $this->assertArrayHasKey(SeedEcosystemDatabase::class, $this->config['dependencies']['invokables']);
        $this->assertArrayHasKey(CreateEcosystemDatabase::class, $this->config['dependencies']['invokables']);
    }

    public function testCommandsAreRegistered(): void
    {
        $this->assertArrayHasKey('commands', $this->config['laminas-cli']);
        $this->assertContains(
            SeedEcosystemDatabase::class,
            $this->config['laminas-cli']['commands']
        );
        $this->assertContains(
            CreateEcosystemDatabase::class,
            $this->config['laminas-cli']['commands']
        );
    }

    public function testGetTemplates(): void
    {
        $this->assertArrayHasKey('paths', $this->config['templates']);
        $this->assertIsArray($this->config['templates']['paths']);
        $this->assertArrayHasKey('ecosystem', $this->config['templates']['paths']);
        $this->assertDirectoryExists($this->config['templates']['paths']['ecosystem'][0]);
    }
}
