<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/tests']);

    // Define what sets should be applied
    $containerConfigurator->import(\Rector\Symfony\Set\SymfonySetList::SYMFONY_60);
    $containerConfigurator->import(\Rector\Symfony\Set\SymfonySetList::SYMFONY_54);
    $containerConfigurator->import(\Rector\Symfony\Set\SymfonySetList::SYMFONY_53);
    $containerConfigurator->import(\Rector\Symfony\Set\SymfonySetList::SYMFONY_52);
    $containerConfigurator->import(\Rector\Symfony\Set\SymfonySetList::SYMFONY_51);
};
