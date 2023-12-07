<?php

namespace ConnectBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;
use ConnectBundle\Tool\Installer;

class ConnectBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/connect/js/pimcore/startup.js',
        ];
    }

    public function getInstaller():Installer
    {
        return $this->container->get(Installer::class);
    }

}
