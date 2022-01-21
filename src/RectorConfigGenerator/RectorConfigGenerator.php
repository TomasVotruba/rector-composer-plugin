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
    const PACKAGES_TO_SET_CONSTANTS = [
        'php' => ['Rector\Set\ValueObject\SetList', 'PHP'],
    ];
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

        $setImportsContent = $this->createSetImportsContent($package, $oldVersion, $newVersion);

        return str_replace('__SET_IMPORTS__', $setImportsContent, $templateFileContents);
    }

    private function createSetImportsContent(string $packageName, string $oldVersion, string $newVersion): string
    {
        $setConstant = self::PACKAGES_TO_SET_CONSTANTS[$packageName] ?? null;
        if ($setConstant === null) {
            return '';
        }

        $setImportsContent = '';

        // @todo complete all PHP versions?
        $oldVersionInt = $this->phpVersionFactory->createIntVersion($oldVersion);
        $newVersionInt = $this->phpVersionFactory->createIntVersion($newVersion);

        $setReference = $setConstant[0] . '::' . $setConstant[1] . '_' . $newVersionInt;

        $setImportsContent .= '$containerConfigurator->import(\\' . $setReference . ');' . PHP_EOL;

        // remove extra new line on the right
        return rtrim($setImportsContent);
    }
}
