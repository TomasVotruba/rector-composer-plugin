<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Version;

final class PhpVersionFactory
{
    public function createIntVersion(string $version): int
    {
        $explodeDash = explode('-', $version);
        if (count($explodeDash) > 1) {
            $version = $explodeDash[0];
        }

        $explodeVersion = explode('.', $version);
        $countExplodedVersion = count($explodeVersion);

        if ($countExplodedVersion >= 2) {
            return (int) $explodeVersion[0] * 10 + (int) $explodeVersion[1];
        }

        return (int) $version;
    }
}
