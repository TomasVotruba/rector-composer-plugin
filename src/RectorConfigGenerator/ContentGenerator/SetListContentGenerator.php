<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\RectorConfigGenerator\ContentGenerator;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Expression;
use Rector\ComposerPlugin\Contract\ConfigContentGeneratorInterface;
use Rector\ComposerPlugin\PhpParser\Printer\IndentAwarePrinter;
use Rector\ComposerPlugin\Repository\SetConstantRepository;
use Rector\ComposerPlugin\ValueObject\PackageVersionChange;
use Rector\ComposerPlugin\ValueObject\SetConstantVersion;
use Rector\ComposerPlugin\Version\PackageSagaResolver;

final class SetListContentGenerator implements ConfigContentGeneratorInterface
{
    /**
     * @var PackageSagaResolver
     */
    private $packageSagaResolver;

    /**
     * @var SetConstantRepository
     */
    private $setConstantRepository;

    /**
     * @var IndentAwarePrinter
     */
    private $indentAwarePrinter;

    public function __construct()
    {
        $this->packageSagaResolver = new PackageSagaResolver();
        $this->setConstantRepository = new SetConstantRepository();
        $this->indentAwarePrinter = new IndentAwarePrinter();
    }

    /**
     * @param PackageVersionChange $packageVersionChange
     * @return SetConstantVersion[]
     */
    public function resolveValues($packageVersionChange): array
    {
        $packageName = $packageVersionChange->getPackageName();

        $setConstant = $this->setConstantRepository->get($packageName);
        if ($setConstant === null) {
            return [];
        }

        $newVersions = $this->packageSagaResolver->resolveNewVersionsFromPackage(
            $packageVersionChange
        );

        $setConstantVersions = [];
        foreach ($newVersions as $newVersion) {
            $setConstantVersions[] = new SetConstantVersion(
                $setConstant,
                $newVersion
            );
        }

        return $setConstantVersions;
    }

    public function getMaskName(): string
    {
        return '__SET_IMPORTS__';
    }

    /**
     * @param SetConstantVersion[] $setConstantVersions
     */
    public function generateContent(array $setConstantVersions): string
    {
        $stmts = [];

        foreach ($setConstantVersions as $setConstantVersion) {
            $classConstFetch = $this->createClassConstFetch($setConstantVersion);

            $args = [new Arg($classConstFetch)];

            $methodCall = new MethodCall(
                new Variable('containerConfigurator'),
                new Identifier('import'),
                $args
            );

            $stmts[] = new Expression($methodCall);
        }

        return $this->indentAwarePrinter->prettyPrintWithIndent($stmts, 2);
    }

    private function createClassConstFetch(SetConstantVersion $setConstantVersion): ClassConstFetch
    {
        $setConstant = $setConstantVersion->getSetConstant();

        $constIdentifier = new Identifier($setConstant[1] . '_' . $setConstantVersion->getVersion());

        return new ClassConstFetch(new FullyQualified($setConstant[0]), $constIdentifier);
    }
}
