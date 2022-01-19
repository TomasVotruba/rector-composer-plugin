<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Tests\Integration\PluginActivation;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @inspiration https://github.com/pyrech/composer-changelogs/blob/main/tests/ChangelogsPluginTest.php
 */
final class PluginActivationTest extends TestCase
{
    /**
     * @var string
     */
    private const TARGET_DIRECTORY = __DIR__ . '/Fixture';

    protected function tearDown(): void
    {
        $filesystem = new Filesystem();

        // remove files created by composer
        $filesystem->remove([
            self::TARGET_DIRECTORY . '/vendor',
            self::TARGET_DIRECTORY . '/composer.lock',
            self::TARGET_DIRECTORY . '/rector.php',
        ]);
    }

    public function testCreateRectorConfig(): void
    {
        $this->assertFileDoesNotExist(self::TARGET_DIRECTORY . '/rector.php');

        $this->runComposerInstallWithDirectory(self::TARGET_DIRECTORY);

        $this->assertFileExists(self::TARGET_DIRECTORY . '/rector.php');

        // @todo assert rector.php content
    }

    private function runComposerInstallWithDirectory(string $directory): void
    {
        $commandLine = sprintf('composer install --working-dir %s --quiet', $directory);
        exec($commandLine);
    }
}
