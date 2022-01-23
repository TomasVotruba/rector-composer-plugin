<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Tests\RectorConfigGenerator;

use Iterator;
use PHPUnit\Framework\TestCase;
use Rector\ComposerPlugin\RectorConfigGenerator\RectorConfigGenerator;

final class RectorConfigGeneratorTest extends TestCase
{
    /**
     * @var RectorConfigGenerator
     */
    private $rectorConfigGenerator;

    protected function setUp(): void
    {
        $this->rectorConfigGenerator = new RectorConfigGenerator();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(array $paths, string $package, string $oldVersion, string $newVersion, string $expectedRectorConfigContent): void
    {
        $rectorConfigContent = $this->rectorConfigGenerator->generate($paths, $package, $oldVersion, $newVersion);

        $this->assertStringEqualsFile(
            $expectedRectorConfigContent,
            $rectorConfigContent
        );
    }

    public function provideData(): Iterator
    {
        yield [['/Fixture'], 'php', '7.4', '8.0', __DIR__ . '/Fixture/single_set.php.inc'];
        yield [['/Fixture'], 'php', '7.3', '8.0', __DIR__ . '/Fixture/two_php_versions.php.inc'];
        yield [['/src', '/tests'], 'symfony/console', '5.0', '6.0', __DIR__ . '/Fixture/symfony_package.php.inc'];
    }
}
