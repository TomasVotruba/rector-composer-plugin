<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\ValueObject;

final class ProjectPassport
{
    /**
     * @var int
     */
    private $minimalPhpVersion;

    /**
     * @var string|null
     */
    private $framework;

    public function __construct(
        // required
        int $minimalPhpVersion,
        ?string $framework
    ) {
        $this->minimalPhpVersion = $minimalPhpVersion;
        $this->framework = $framework;
    }

    public function getMinimalPhpVersion(): int
    {
        return $this->minimalPhpVersion;
    }

    public function getFramework(): ?string
    {
        return $this->framework;
    }
}
