<?php

declare(strict_types=1);

/*
 * This file is part of the Explicit Architecture POC,
 * which is created on top of the Symfony Demo application.
 *
 * (c) Herberto Graça <herberto.graca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Infrastructure\Framework\Symfony;

use Exception;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * @author Javier Eguiluz
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const ENV_PROD = 'prod';

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    private function getConfDir(): string
    {
        return $this->getProjectDir() . '/config';
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function registerBundles()
    {
        /** @var bool[][] $contents */
        $contents = require $this->getConfDir() . '/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getConfDir();

        $this->configureParameters($loader, $confDir);
        $this->configurePackages($loader, $confDir);
        $this->configureServices($loader, $confDir);
    }

    /**
     * @throws FileLoaderLoadException
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getConfDir();

        $environmentList = array_unique([self::ENV_PROD, $this->environment]);
        foreach ($environmentList as $environment) {
            // Routes can not be bulk imported because they need to be ordered
            $routes->import($confDir . '/routes/{' . $environment . '}/index' . self::CONFIG_EXTS, '/', 'glob');
        }
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        /** @var bool[][] $contents */
        $contents = require $this->getConfDir() . '/compiler_pass.php';
        foreach ($contents as $compilerPass => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                $containerBuilder->addCompilerPass(new $compilerPass());
            }
        }
    }

    /**
     * @throws Exception
     */
    private function configureParameters(LoaderInterface $loader, string $confDir): void
    {
        $environmentList = array_unique([self::ENV_PROD, $this->environment]);
        foreach ($environmentList as $environment) {
            $loader->load($confDir . '/parameters/{' . $environment . '}' . self::CONFIG_EXTS, 'glob');
        }
    }

    /**
     * @throws Exception
     */
    private function configurePackages(LoaderInterface $loader, string $confDir): void
    {
        $loader->load($confDir . '/packages/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/packages/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
    }

    /**
     * @throws Exception
     */
    private function configureServices(LoaderInterface $loader, string $confDir): void
    {
        $environmentList = array_unique([self::ENV_PROD, $this->environment]);
        foreach ($environmentList as $environment) {
            $loader->load($confDir . '/services/{' . $environment . '}' . self::CONFIG_EXTS, 'glob');
            $loader->load($confDir . '/services/{' . $environment . '}/**/*' . self::CONFIG_EXTS, 'glob');
        }
    }
}
