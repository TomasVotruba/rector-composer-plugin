<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\ValueObject;

final class SetConstantVersion
{
    /**
     * @var string[]
     */
    private $setConstant;

    /**
     * @var int
     */
    private $version;

    /**
     * @param string[] $setConstant
     */
    public function __construct(
        array $setConstant,
        int $version
    ) {
        $this->setConstant = $setConstant;
        $this->version = $version;
    }

    /**
     * @return string[]
     */
    public function getSetConstant(): array
    {
        return $this->setConstant;
    }

    public function getVersion(): int
    {
        return $this->version;
    }
}
