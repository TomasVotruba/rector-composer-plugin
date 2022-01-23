<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Version;

use Rector\ComposerPlugin\Exception\InvalidSagaVersionException;
use Rector\ComposerPlugin\ValueObject\PackageVersionChange;

/**
 * @see \Rector\ComposerPlugin\Tests\Version\PackageSagaResolverTest
 */
final class PackageSagaResolver
{
    /**
     * @var PhpVersionFactory
     */
    private $phpVersionFactory;

    /**
     * @var PackageSagaProvider
     */
    private $packageSagaProvider;

    public function __construct()
    {
        $this->phpVersionFactory = new PhpVersionFactory();
        $this->packageSagaProvider = new PackageSagaProvider();
    }

    /**
     * @return int[]
     */
    public function resolveNewVersionsFromPackage(PackageVersionChange $packageVersionChange): array
    {
        $packageName = $packageVersionChange->getPackageName();

        $packageSaga = $this->packageSagaProvider->provideForPackage($packageName);

        $oldVersion = $packageVersionChange->getOldVersion();
        $newVersion = $packageVersionChange->getNewVersion();

        $oldVersionInt = $this->phpVersionFactory->createIntVersion($oldVersion);
        $newVersionInt = $this->phpVersionFactory->createIntVersion($newVersion);

        $oldVersionKey = $this->findVersionKey($packageSaga, $packageName, $oldVersionInt);
        $newVersionKey = $this->findVersionKey($packageSaga, $packageName, $newVersionInt);

        return $this->resolveNewVersions($packageSaga, $oldVersionKey, $newVersionKey);
    }

    /**
     * @param array<int, int> $packageSaga
     */
    private function findVersionKey(array $packageSaga, string $packageName, int $versionInt): int
    {
        $versionKey = array_search($versionInt, $packageSaga, true);

        if ($versionKey === false) {
            $errorMessage = sprintf('Version %d was not found in "%s" package available versions.', $versionInt, $packageName);
            throw new InvalidSagaVersionException($errorMessage);
        }

        return $versionKey;
    }

    /**
     * @param int[] $packageSaga
     * @return int[]
     */
    private function resolveNewVersions(array $packageSaga, int $oldVersionKey, int $newVersionKey): array
    {
        $newVersionKeys = range($oldVersionKey + 1, $newVersionKey);

        $newVersions = [];
        foreach ($newVersionKeys as $newVersionKey) {
            $newVersions[] = $packageSaga[$newVersionKey];
        }

        // make the newest version to the top
        return array_reverse($newVersions);
    }
}
