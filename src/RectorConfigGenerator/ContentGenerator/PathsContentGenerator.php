<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\RectorConfigGenerator\ContentGenerator;

use Nette\Utils\Strings;
use Rector\ComposerPlugin\Contract\ConfigContentGeneratorInterface;
use Rector\ComposerPlugin\FileSystem\PathNormalizer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\ComposerPlugin\Tests\RectorConfigGenerator\ContentGenerator\PathsContentGeneratorTest
 */
final class PathsContentGenerator implements ConfigContentGeneratorInterface
{
    /**
     * @var string
     */
    public const MASK_NAME = '__PATHS__';

    /**
     * @var PathNormalizer
     */
    private $pathNormalizer;

    public function __construct()
    {
        $this->pathNormalizer = new PathNormalizer();
    }

    /**
     * @param string[] $paths
     */
    public function generateContent(array $paths): string
    {
        Assert::allString($paths);

        $pathsContent = '';
        foreach ($paths as $path) {
            $path = $this->pathNormalizer->normalize($path);

            // @todo use node printer
            $pathsContent .= '        __DIR__ . \'/' . $path . "'," . PHP_EOL;
        }

        // remove extra new line on the right
        return rtrim($pathsContent);
    }

    /**
     * @param string $currentWorkingDirectory
     * @return string[]
     */
    public function resolveValues($currentWorkingDirectory): array
    {
        $finder = new Finder();
        $fileInfosIterator = $finder->directories()
            ->in($currentWorkingDirectory)
            ->depth(0)
            ->getIterator();

        $fileInfos = iterator_to_array($fileInfosIterator);

        return $this->resolveRelativePathToDirectory($fileInfos, $currentWorkingDirectory);
    }

    public function getMaskName(): string
    {
        return self::MASK_NAME;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return string[]
     */
    private function resolveRelativePathToDirectory(array $fileInfos, string $rootDirectory): array
    {
        $relativeDirectoryPaths = [];

        foreach ($fileInfos as $fileInfo) {
            $realFilePath = $fileInfo->getRealPath();
            Assert::string($realFilePath);

            $relativeDirectoryPath = Strings::after($realFilePath, $rootDirectory);
            Assert::string($relativeDirectoryPath);

            $relativeDirectoryPaths[] = $this->pathNormalizer->normalize($relativeDirectoryPath);
        }

        return $relativeDirectoryPaths;
    }
}
