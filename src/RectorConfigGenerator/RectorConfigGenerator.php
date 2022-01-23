<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\RectorConfigGenerator;

use Nette\Utils\FileSystem;
use Rector\ComposerPlugin\RectorConfigGenerator\ContentGenerator\PathsContentGenerator;
use Rector\ComposerPlugin\RectorConfigGenerator\ContentGenerator\SetListContentGenerator;
use Rector\ComposerPlugin\ValueObject\PackageVersionChange;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\ComposerPlugin\Tests\RectorConfigGenerator\RectorConfigGeneratorTest
 */
final class RectorConfigGenerator
{
    /**
     * @var string
     */
    private const RECTOR_CONFIG_TEMPLATE = __DIR__ . '/../../templates/rector.php.dist';

    /**
     * @var PathsContentGenerator
     */
    private $pathsContentGenerator;

    /**
     * @var SetListContentGenerator
     */
    private $setListContentGenerator;

    public function __construct()
    {
        $this->setListContentGenerator = new SetListContentGenerator();
        $this->pathsContentGenerator = new PathsContentGenerator();
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
        Assert::allString($paths);

        $templateFileContents = FileSystem::read(self::RECTOR_CONFIG_TEMPLATE);

        $packageVersionChange = new PackageVersionChange($package, $oldVersion, $newVersion);

        $setConstantVersion = $this->setListContentGenerator->resolveValues($packageVersionChange);
        $setImportsContent = $this->setListContentGenerator->generateContent($setConstantVersion);

        $templateFileContents = str_replace($this->setListContentGenerator->getMaskName(), $setImportsContent, $templateFileContents);

        if ($paths !== []) {
            $pathsContent = $this->pathsContentGenerator->generateContent($paths);
            $templateFileContents = str_replace($this->pathsContentGenerator->getMaskName(), $pathsContent, $templateFileContents);
        }

        return $templateFileContents;
    }
}
