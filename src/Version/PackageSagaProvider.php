<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Version;

use Nette\Utils\Strings;
use Rector\ComposerPlugin\Exception\InvalidSagaVersionException;
use Rector\ComposerPlugin\Repository\SetConstantRepository;

/**
 * @see \Rector\ComposerPlugin\Tests\Version\PackageSagaProviderTest
 */
final class PackageSagaProvider
{
    /**
     * @var SetConstantRepository
     */
    private $setConstantRepository;

    public function __construct()
    {
        $this->setConstantRepository = new SetConstantRepository();
    }

    /**
     * @return int[]
     */
    public function provideForPackage(string $packageName): array
    {
        $setConstant = $this->setConstantRepository->get($packageName);
        if ($setConstant === null) {
            $errorMessage = sprintf('Package "%s" not found in available sets', $packageName);
            throw new InvalidSagaVersionException($errorMessage);
        }

        $setClass = $setConstant[0];
        $constantPrefix = $setConstant[1];

        $setClassReflection = new \ReflectionClass($setClass);
        $constantNames = array_keys($setClassReflection->getConstants());

        $constantPrefix .= '_';

        $versions = [];

        foreach ($constantNames as $constantName) {
            if (! Strings::startsWith($constantName, $constantPrefix)) {
                continue;
            }

            $version = Strings::after($constantName, $constantPrefix);

            // subversion tests
            if (! is_numeric($version)) {
                continue;
            }

            $versions[] = (int) $version;
        }

        return $versions;
    }
}
