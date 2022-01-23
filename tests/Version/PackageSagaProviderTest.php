<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Tests\Version;

use PHPUnit\Framework\TestCase;
use Rector\ComposerPlugin\Version\PackageSagaProvider;

final class PackageSagaProviderTest extends TestCase
{
    /**
     * @var PackageSagaProvider
     */
    private $packageSagaProvider;

    protected function setUp(): void
    {
        $this->packageSagaProvider = new PackageSagaProvider();
    }

    public function test(): void
    {
        $versions = $this->packageSagaProvider->provideForPackage('php');

        $this->assertCount(12, $versions);
        $this->assertSame([52, 53, 54, 55, 56, 70, 71, 72, 73, 74, 80, 81], $versions);
    }
}
