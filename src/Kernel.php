<?php

declare(strict_types=1);

namespace Libero\DummyApi;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getProjectDir() : string
    {
        return __DIR__.'/..';
    }

    public function getCacheDir() : string
    {
        return "{$this->getProjectDir()}/var/cache/{$this->environment}";
    }

    public function getLogDir() : string
    {
        return "{$this->getProjectDir()}/var/log";
    }

    public function registerBundles() : iterable
    {
        $contents = require "{$this->getProjectDir()}/config/bundles.php";
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader) : void
    {
        $confDir = "{$this->getProjectDir()}/config";

        $container->addResource(new DirectoryResource($confDir));
        $container->setParameter('container.dumper.inline_class_loader', true);

        $loader->load("{$confDir}/{packages}/*".self::CONFIG_EXTS, 'glob');
        $loader->load("{$confDir}/{packages}/{$this->environment}/**/*".self::CONFIG_EXTS, 'glob');
        $loader->load("{$confDir}/{services}".self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes) : void
    {
        $confDir = "{$this->getProjectDir()}/config";

        $routes->import("{$confDir}/{routes}/{$this->environment}/**/*".self::CONFIG_EXTS, '/', 'glob');
        $routes->import("{$confDir}/{routes}".self::CONFIG_EXTS, '/', 'glob');
    }
}
