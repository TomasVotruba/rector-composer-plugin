<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Tests\Version;

use PHPUnit\Framework\TestCase;
use Rector\ComposerPlugin\ValueObject\PackageVersionChange;
use Rector\ComposerPlugin\Version\PackageSagaResolver;

final class PackageSagaResolverTest extends TestCase
{
    /**
     * @var PackageSagaResolver
     */
    private $packageSagaResolver;

    protected function setUp(): void
    {
        $this->packageSagaResolver = new PackageSagaResolver();
    }

    public function test(): void
    {
        $packageVersionChange = new PackageVersionChange('php', '5.3', '7.0');

        $newVersions = $this->packageSagaResolver->resolveNewVersionsFromPackage($packageVersionChange);

        $this->assertSame([70, 56,  55, 54], $newVersions);
    }
}
