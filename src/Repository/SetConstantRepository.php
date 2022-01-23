<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\Repository;

use Nette\Utils\Strings;
use Rector\CakePHP\Set\CakePHPSetList;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Laravel\Set\LaravelSetList;
use Rector\Nette\Set\NetteSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Set\TwigSetList;

final class SetConstantRepository
{
    /**
     * @var array<string, array<string>>
     */
    private const PACKAGES_TO_SET_CONSTANTS = [
        'php' => [SetList::class, 'PHP'],
        'symfony' => [SymfonySetList::class, 'SYMFONY'],
        'laravel' => [LaravelSetList::class, 'LARAVEL'],
        'phpunit' => [PHPUnitSetList::class, 'PHPUNIT'],
        'nette' => [NetteSetList::class, 'NETTE'],
        'cakephp' => [CakePHPSetList::class, 'CAKEPHP'],
        'doctrine' => [DoctrineSetList::class, 'DOCTRINE'],
        'twig' => [TwigSetList::class, 'TWIG'],
    ];

    /**
     * @return string[]|null
     */
    public function get(string $packageName): ?array
    {
        if (Strings::contains($packageName, '/')) {
            // to get "symfony" from "symfony/console"
            $vendorName = Strings::before($packageName, '/');
        } else {
            $vendorName = $packageName;
        }

        return self::PACKAGES_TO_SET_CONSTANTS[$vendorName] ?? null;
    }
}
