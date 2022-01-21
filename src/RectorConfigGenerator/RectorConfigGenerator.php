<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\RectorConfigGenerator;

use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @see \Rector\ComposerPlugin\Tests\RectorConfigGenerator\RectorConfigGeneratorTest
 */
final class RectorConfigGenerator
{
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct()
    {
        $this->smartFileSystem = new SmartFileSystem();
    }

    /**
     * @param string[] $paths
     */
    public function generate(
        array $paths,
        string $package,
        string $oldVersion,
        string $newVersion
    ): string {
        $bareRectorTemplate = __DIR__ . '/../../templates/rector.php.dist';

        $smartFileContents = $this->smartFileSystem->readFile($bareRectorTemplate);

        return $smartFileContents;
    }
}
