<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Contract;

interface ConfigContentGeneratorInterface
{
    public function getMaskName(): string;

    /**
     * @param mixed $criteria
     * @return mixed[]
     */
    public function resolveValues($criteria): array;

    /**
     * @param mixed[] $values
     */
    public function generateContent(array $values): string;
}
