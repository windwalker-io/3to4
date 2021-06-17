<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Legacy\Core\Package;

use Windwalker\Legacy\Core\Mvc\ControllerResolver;
use Windwalker\Legacy\Core\Mvc\MvcResolver;
use Windwalker\Legacy\Core\Mvc\RepositoryResolver;
use Windwalker\Legacy\Core\Mvc\ViewResolver;
use Windwalker\Legacy\Core\Package\Resolver\DataMapperResolver;
use Windwalker\Legacy\Core\Package\Resolver\RecordResolver;
use Windwalker\Legacy\Core\Router\PackageRouter;
use Windwalker\Legacy\DI\Container;
use Windwalker\Legacy\DI\ServiceProviderInterface;
use Windwalker\Legacy\Form\FieldHelper;
use Windwalker\Legacy\Form\ValidatorHelper;

/**
 * The PackageProvider class.
 *
 * @since  3.0
 * @deprecated Legacy code
 */
class PackageProvider implements ServiceProviderInterface
{
    use PackageAwareTrait;

    /**
     * PackageProvider constructor.
     *
     * @param AbstractPackage $package
     */
    public function __construct(AbstractPackage $package)
    {
        $this->package = $package;
    }

    /**
     * boot
     *
     * @return  void
     * @throws \ReflectionException
     */
    public function boot()
    {
        $ns = (new \ReflectionClass($this->package))->getNamespaceName();

        RecordResolver::addNamespace($ns . '\Record');
        DataMapperResolver::addNamespace($ns . '\DataMapper');
        FieldHelper::addNamespace($ns . '\Field');
        ValidatorHelper::addNamespace($ns . 'Validator');
        // FieldDefinitionResolver::addNamespace($ns . '\Form');
    }

    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container $container The DI container.
     *
     * @return  void
     */
    public function register(Container $container)
    {
        $container->share(get_class($this->package), $this->package)
            ->bindShared(AbstractPackage::class, get_class($this->package));

        $container->share('controller.resolver', function (Container $container) {
            return new ControllerResolver($this->package, $container);
        });

        $container->share('repository.resolver', function (Container $container) {
            return new RepositoryResolver($this->package, $container);
        })->alias('model.resolver', 'repository.resolver');

        $container->share('view.resolver', function (Container $container) {
            return new ViewResolver($this->package, $container);
        });

        $container->share('mvc.resolver', function (Container $container) {
            return new MvcResolver(
                $container->get('controller.resolver'),
                $container->get('repository.resolver'),
                $container->get('view.resolver')
            );
        });

        if ($this->package->app->isWeb()) {
            // Router
            $container->prepareSharedObject(PackageRouter::class)->alias('router', PackageRouter::class);
        }
    }
}
