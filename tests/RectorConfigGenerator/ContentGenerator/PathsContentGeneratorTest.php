<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Tests\RectorConfigGenerator\ContentGenerator;

use PHPUnit\Framework\TestCase;
use Rector\ComposerPlugin\RectorConfigGenerator\ContentGenerator\PathsContentGenerator;

final class PathsContentGeneratorTest extends TestCase
{
    /**
     * @var PathsContentGenerator
     */
    private $pathsContentGenerator;

    protected function setUp(): void
    {
        $this->pathsContentGenerator = new PathsContentGenerator();
    }

    public function test(): void
    {
        $resolvedPaths = $this->pathsContentGenerator->resolveValues(__DIR__ . '/Source');
        $this->assertSame(['src', 'tests'], $resolvedPaths);

        $pathsContents = $this->pathsContentGenerator->generateContent($resolvedPaths);
        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_paths_content.php.inc', $pathsContents . PHP_EOL);
    }
}
