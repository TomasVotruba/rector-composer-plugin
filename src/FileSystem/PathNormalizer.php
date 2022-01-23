<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\FileSystem;

final class PathNormalizer
{
    /**
     * Make path compatible with *nix and Windows systems
     */
    public function normalize(string $path): string
    {
        return ltrim($path, '\\/');
    }
}
