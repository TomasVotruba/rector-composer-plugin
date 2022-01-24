<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [__DIR__ . '/Fixture']);

    // Define what sets should be applied
    $containerConfigurator->import(\Rector\Set\ValueObject\SetList::PHP_80);
    $containerConfigurator->import(\Rector\Set\ValueObject\SetList::PHP_74);
};
