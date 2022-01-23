<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin;

use Composer\Composer;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Rector\ComposerPlugin\RectorConfigGenerator\RectorConfigGenerator;

/**
 * @inspiration https://github.com/rectorphp/extension-installer/blob/89e2519dc01c4350829812925e0f674dcb310425/src/PluginInstaller.php#L16
 * https://github.com/cweagans/composer-patches/blob/master/src/Plugin/Patches.php
 */
final class RectorPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var RectorConfigGenerator
     */
    private $rectorConfigGenerator;

    public function __construct()
    {
        $this->rectorConfigGenerator = new RectorConfigGenerator();
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
        $composerFile = Factory::getComposerFile();
        $projectDirectory = dirname($composerFile);

        $rectorConfigFilePath = $projectDirectory . '/rector.php';
        if (file_exists($rectorConfigFilePath)) {
            // file already exists, skip it
            return;
        }

        $io->write('  - Creating <info>rector.php</info> file');

        // @todo create dummy rector.php file based on PHP version
        file_put_contents($rectorConfigFilePath, '<?php // rector config' . PHP_EOL);
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // not used
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // not used
    }

    public function postPackageUpdate(PackageEvent $packageEvent): void
    {
        $updateOperation = $packageEvent->getOperation();
        if (! $updateOperation instanceof UpdateOperation) {
            return;
        }

        $completeInitialPackage = $updateOperation->getInitialPackage();
        $completeTargetPackage = $updateOperation->getTargetPackage();

        $this->rectorConfigGenerator->generate(
            [],
            $packageEvent->getName(),
            $completeInitialPackage->getVersion(),
            $completeTargetPackage->getVersion()
        );
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // this constant cannot be discovered anywhere
            // as it is invoked magically here https://github.com/composer/composer/blob/e6cfc924f24089bc02cf8f4d27367b283247610e/src/Composer/Installer/InstallationManager.php#L458
            PackageEvents::POST_PACKAGE_UPDATE => 'postPackageUpdate',
        ];
    }
}
