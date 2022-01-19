<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Tests\Integration\PluginActivation;

use Rector\ComposerPlugin\Tests\Integration\AbstractComposerPluginRunTestCase;

/**
 * @inspiration https://github.com/pyrech/composer-changelogs/blob/main/tests/ChangelogsPluginTest.php
 */
final class PluginActivationTest extends AbstractComposerPluginRunTestCase
{
    /**
     * @var string
     */
    private const TARGET_DIRECTORY = __DIR__ . '/Fixture';

    protected function tearDown(): void
    {
        $this->cleanDirectory(self::TARGET_DIRECTORY);
    }

    public function testCreateRectorConfig(): void
    {
        $this->assertFileDoesNotExist(self::TARGET_DIRECTORY . '/rector.php');

        $this->runComposerInstallWithDirectory(self::TARGET_DIRECTORY);

        $this->assertFileExists(self::TARGET_DIRECTORY . '/rector.php');

        // @todo assert rector.php content
    }
}
