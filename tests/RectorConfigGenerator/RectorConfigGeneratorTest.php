<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Tests\RectorConfigGenerator;

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

    public function test(): void
    {
        // @todo start simple with single change
        $rectorConfigContent = $this->rectorConfigGenerator->generate([__DIR__ . '/Fixture'], 'php', '7.4', '8.0');

        $this->assertStringEqualsFile(
            __DIR__ . '/Fixture/expected_php_rector_config_content.php',
            $rectorConfigContent
        );
    }
}
