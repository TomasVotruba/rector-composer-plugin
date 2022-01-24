<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\RectorConfigGenerator\ContentGenerator;

use Nette\Utils\Strings;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use Rector\ComposerPlugin\Contract\ConfigContentGeneratorInterface;
use Rector\ComposerPlugin\FileSystem\PathNormalizer;
use Rector\ComposerPlugin\PhpParser\Printer\IndentAwarePrinter;
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

    /**
     * @var IndentAwarePrinter
     */
    private $indentAwarePrinter;

    public function __construct()
    {
        $this->pathNormalizer = new PathNormalizer();
        $this->indentAwarePrinter = new IndentAwarePrinter();
    }

    /**
     * @param string[] $paths
     */
    public function generateContent(array $paths): string
    {
        Assert::allString($paths);

        $stmts = [];

        $pathArrayItems = [];
        foreach ($paths as $path) {
            $path = '/' . $this->pathNormalizer->normalize($path);

            $concat = new Concat(new Dir(), new String_($path));
            $pathArrayItems[] = new ArrayItem($concat);
        }

        $pathsArray = new Array_($pathArrayItems, [
            'kind' => Array_::KIND_SHORT,
        ]);

        $stmts[] = $this->createSetParameterMethodCall('PATHS', $pathsArray);

        return $this->indentAwarePrinter->prettyPrintWithIndent($stmts);
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

    private function createSetParameterMethodCall(string $parameterName, Expr $expr): Stmt
    {
        $args = [
            new Arg(new ClassConstFetch(new Name('Option'), new Identifier($parameterName))),
            new Arg($expr),
        ];

        $parametersSetMethodCall = new MethodCall(new Variable('parameters'), new Identifier('set'), $args);

        return new Expression($parametersSetMethodCall);
    }
}
