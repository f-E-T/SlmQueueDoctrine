<?php

namespace SlmQueueDoctrineTest;

use PHPUnit\Framework\TestCase;
use SlmQueueDoctrine\ConfigProvider;
use SlmQueueDoctrine\Module;

class ConfigProviderTest extends TestCase
{
    public function testConfigProviderGetConfig(): void
    {
        $configProvider = new ConfigProvider();
        $config = $configProvider();

        static::assertNotEmpty($config['dependencies']);
        static::assertNotEmpty($config['slm_queue']);
        static::assertNotEmpty($config['laminas-cli']);
    }

    public function testModuleConfigEqualsConfigProviderWithServiceManagerKey(): void
    {
        $config = (new ConfigProvider())();
        $moduleConfig = (new Module())->getConfig();

        static::assertSame($config['dependencies'], $moduleConfig['service_manager']);
        static::assertArrayNotHasKey('dependencies', $moduleConfig);
        static::assertSame($config['slm_queue'], $moduleConfig['slm_queue']);
        static::assertSame($config['laminas-cli'], $moduleConfig['laminas-cli']);
    }
}
