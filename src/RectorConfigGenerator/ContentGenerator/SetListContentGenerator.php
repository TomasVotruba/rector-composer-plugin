<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\RectorConfigGenerator\ContentGenerator;

use Rector\ComposerPlugin\Contract\ConfigContentGeneratorInterface;
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

    public function __construct()
    {
        $this->packageSagaResolver = new PackageSagaResolver();
        $this->setConstantRepository = new SetConstantRepository();
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
        $setImportsContent = '';

        foreach ($setConstantVersions as $setConstantVersion) {
            $setConstant = $setConstantVersion->getSetConstant();

            $setReference = $setConstant[0] . '::' . $setConstant[1] . '_' . $setConstantVersion->getVersion();

            $setImportsContent .= '    $containerConfigurator->import(\\' . $setReference . ');' . PHP_EOL;
        }

        // remove extra new line on the right
        return rtrim($setImportsContent);
    }
}
