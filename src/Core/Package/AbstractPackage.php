<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2016 LYRASOFT. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

namespace Windwalker\Legacy\Core\Package;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Windwalker\Legacy\Console\Command\Command;
use Windwalker\Legacy\Console\Console;
use Windwalker\Legacy\Core\Application\Middleware\AbstractWebMiddleware;
use Windwalker\Legacy\Core\Application\ServiceAwareInterface;
use Windwalker\Legacy\Core\Application\ServiceAwareTrait;
use Windwalker\Legacy\Core\Application\WebApplication;
use Windwalker\Legacy\Core\Console\CoreConsole;
use Windwalker\Legacy\Core\Controller\AbstractController;
use Windwalker\Legacy\Core\Controller\CallbackController;
use Windwalker\Legacy\Core\Event\EventDispatcher;
use Windwalker\Legacy\Core\Mvc\MvcResolver;
use Windwalker\Legacy\Core\Provider\BootableDeferredProviderInterface;
use Windwalker\Legacy\Core\Provider\BootableProviderInterface;
use Windwalker\Legacy\Core\Router\MainRouter;
use Windwalker\Legacy\Core\Router\PackageRouter;
use Windwalker\Legacy\Core\Router\RouteCreator;
use Windwalker\Legacy\Core\Router\RouteString;
use Windwalker\Legacy\Core\Security\CsrfGuard;
use Windwalker\Legacy\Core\View\AbstractView;
use Windwalker\Legacy\DI\ClassMeta;
use Windwalker\Legacy\DI\Container;
use Windwalker\Legacy\DI\ContainerAwareTrait;
use Windwalker\Legacy\DI\ServiceProviderInterface;
use Windwalker\Legacy\Event\DispatcherAwareInterface;
use Windwalker\Legacy\Event\DispatcherInterface;
use Windwalker\Legacy\Event\EventInterface;
use Windwalker\Legacy\Event\EventTriggerableInterface;
use Windwalker\Legacy\Event\ListenerPriority;
use Windwalker\Legacy\Filesystem\File;
use Windwalker\Legacy\Http\Response\RedirectResponse;
use Windwalker\Legacy\IO\Input;
use Windwalker\Legacy\IO\PsrInput;
use Windwalker\Legacy\Middleware\Chain\Psr7ChainBuilder;
use Windwalker\Legacy\Middleware\Psr7Middleware;
use Windwalker\Legacy\Router\Exception\RouteNotFoundException;
use Windwalker\Legacy\Structure\Structure;
use Windwalker\Legacy\Utilities\Arr;
use Windwalker\Legacy\Utilities\Queue\PriorityQueue;
use Windwalker\Legacy\Utilities\Reflection\ReflectionHelper;

/**
 * The AbstractPackage class.
 *
 * @since  2.0
 * @deprecated Legacy code
 */
class AbstractPackage
{
    /**
     * getFile
     *
     * @return  string
     * @throws \ReflectionException
     */
    public function getFile()
    {
        $ref = new \ReflectionClass(static::class);

        return $ref->getFileName();
    }

    /**
     * getDir
     *
     * @return  string
     * @throws \ReflectionException
     */
    public function getDir()
    {
        return dirname($this->getFile());
    }

    /**
     * dir
     *
     * @return  string
     *
     * @throws \ReflectionException
     *
     * @since  3.5
     */
    public static function dir(): string
    {
        return dirname(static::file());
    }

    /**
     * file
     *
     * @return  string
     *
     * @throws \ReflectionException
     *
     * @since  3.5
     */
    public static function file(): string
    {
        return (new \ReflectionClass(static::class))->getFileName();
    }

    /**
     * getNamespace
     *
     * @return  string
     *
     * @since  3.1
     */
    public function getNamespace()
    {
        return ReflectionHelper::getNamespaceName($this);
    }
}
