<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractComposerPluginRunTestCase extends TestCase
{
    protected function cleanDirectory(string $directory): void
    {
        $filesystem = new Filesystem();

        // remove files created by composer
        $filesystem->remove([
            $directory . '/vendor',
            $directory . '/composer.lock',
            $directory . '/rector.php',
        ]);
    }

    protected function runComposerInstallWithDirectory(string $directory): void
    {
        // "--quiet" to silent composer output during test run
        $commandLine = sprintf('composer install --working-dir %s --quiet', $directory);
        exec($commandLine);
    }
}
