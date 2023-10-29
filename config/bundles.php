<?php

use Pimcore\Bundle\ApplicationLoggerBundle\PimcoreApplicationLoggerBundle;
use Pimcore\Bundle\CustomReportsBundle\PimcoreCustomReportsBundle;
use Pimcore\Bundle\SimpleBackendSearchBundle\PimcoreSimpleBackendSearchBundle;
use Pimcore\Bundle\BundleGeneratorBundle\PimcoreBundleGeneratorBundle;

return [
    //Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    PimcoreApplicationLoggerBundle::class => ['all' => true],
    PimcoreCustomReportsBundle::class => ['all' => true],
    PimcoreSimpleBackendSearchBundle::class => ['all' => true],
    PimcoreBundleGeneratorBundle::class => ['all' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    DemoBundle\DemoBundle::class => ['all' => true],
    CheckBundle\CheckBundle::class => ['all' => true],
    ProductBundle\ProductBundle::class => ['all' => true],
    SpyBundle\SpyBundle::class => ['all' => true],
];
