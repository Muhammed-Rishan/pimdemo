<?php

namespace SpyBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;
use SpyBundle\Tool\Installer;

class SpyBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/spy/js/pimcore/startup.js',
            '/bundles/spy/js/pimcore/Button.js',
        ];
    }


    public function getInstaller():Installer
    {
        return $this->container->get(Installer::class);
    }
}
