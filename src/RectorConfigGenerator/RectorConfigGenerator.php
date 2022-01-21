<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\RectorConfigGenerator;

use Rector\ComposerPlugin\Version\PhpVersionFactory;
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

    /**
     * @var PhpVersionFactory
     */
    private $phpVersionFactory;

    public function __construct()
    {
        $this->smartFileSystem = new SmartFileSystem();
        $this->phpVersionFactory = new PhpVersionFactory();
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

        $templateFileContents = $this->smartFileSystem->readFile($bareRectorTemplate);

        $packagesToSetConstants = [
            'php' => ['Rector\Set\ValueObject\SetList', 'PHP'],
        ];

        $setConstant = $packagesToSetConstants[$package] ?? null;
        if ($setConstant !== null) {
            // @todo complete all PHP versions?
            $oldVersionInt = $this->phpVersionFactory->createIntVersion($oldVersion);
            $newVersionInt = $this->phpVersionFactory->createIntVersion($newVersion);

            $setReference = $setConstant[0] . '::' . $setConstant[1] . '_' . $newVersionInt;

            $setImportsContent = '$containerConfigurator->import(\\' . $setReference . ');';

            return str_replace('__SET_IMPORTS__', $setImportsContent, $templateFileContents);
        }

        return $templateFileContents;
    }
}
