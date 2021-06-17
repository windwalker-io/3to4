<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2016 LYRASOFT. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

namespace Windwalker\Legacy\Core;

use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Core\Runtime\Config;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\DI\Container;

/**
 * The Factory class.
 *
 * @since  2.0
 */
abstract class Ioc
{
    /**
     * getInstance
     *
     * @param string $name
     * @param string $profile
     *
     * @return Container
     */
    public static function factory()
    {
        return \W3to4\Ioc::getContainer();
    }

    /**
     * getContainer
     *
     * @param   string $name
     * @param   string $profile
     *
     * @return  Container
     */
    public static function getContainer()
    {
        return static::factory();
    }

    /**
     * setContainer
     *
     * @param   string    $profile
     * @param   Container $container
     *
     * @return  void
     */
    public static function setContainer($profile, Container $container)
    {
        //
    }

    /**
     * setProfile
     *
     * @param string $name
     *
     * @return  void
     */
    public static function setProfile($name = 'windwalker')
    {
        //
    }

    /**
     * Method to get property Profile
     *
     * @return  string
     */
    public static function getProfile()
    {
        return 'windwalker';
    }

    /**
     * reset
     *
     * @param string $profile
     *
     * @return  void
     */
    public static function reset($profile = null)
    {
        //
    }

    /**
     * getApplication
     *
     * @return  ApplicationInterface
     */
    public static function getApplication()
    {
        return \W3to4\Ioc::getAppContext() ?? \W3to4\Ioc::getRootApp();
    }

    /**
     * getConfig
     *
     * @return  Config
     */
    public static function getConfig()
    {
        return static::getContainer()->getParameters();
    }

    /**
     * getInput
     *
     * @return  \Windwalker\Legacy\IO\Input
     */
    public static function getInput()
    {
        return static::get('input');
    }

    /**
     * getDatabase
     *
     * @return  object|DatabaseAdapter
     */
    public static function getDatabase()
    {
        return static::getApplication()->service(DatabaseAdapter::class);
    }

    /**
     * getRouter
     *
     * @return  object|Navigator
     */
    public static function getRouter()
    {
        return static::getApplication()->service(Navigator::class);
    }

    /**
     * getLanguage
     *
     * @return  object|LangService
     */
    public static function getLanguage()
    {
        return static::getApplication()->service(LangService::class);
    }

    /**
     * getUriData
     *
     * @return  object|SystemUri
     */
    public static function getUriData()
    {
        return static::getApplication()->service(SystemUri::class);
    }

    /**
     * make
     *
     * @param string $key
     * @param array  $args
     * @param bool   $protected
     *
     * @return  mixed
     *
     * @since  3.4.2
     */
    public static function make(string $key, array $args = [], bool $protected = false)
    {
        return static::getContainer()->createSharedObject($key, $args, $protected);
    }

    /**
     * service
     *
     * @param string $class
     * @param bool   $forceNew
     *
     * @return  mixed
     *
     * @since  3.5.5
     */
    public static function service(string $class, bool $forceNew = false)
    {
        /** @var Container $container */
        $container = static::getContainer();

        if (!$forceNew && $container->has($class)) {
            return $container->get($class);
        }

        return $container->createSharedObject($class);
    }

    /**
     * Convenience method for creating shared keys.
     *
     * @param   string   $key      Name of dataStore key to set.
     * @param   callable $callback Callable function to run when requesting the specified $key.
     * @param   string   $name     Container name.
     *
     * @return  Container This object for chaining.
     *
     * @since    2.0
     */
    public function share($key, $callback, $name = null)
    {
        return static::factory($name)->share($key, $callback);
    }

    /**
     * exists
     *
     * @param string $key
     * @param string $child
     *
     * @return  boolean
     */
    public static function exists($key, $child = null)
    {
        return static::factory($child)->has($key);
    }
}
