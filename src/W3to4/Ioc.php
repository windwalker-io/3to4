<?php

/**
 * Part of datavideo project.
 *
 * @copyright  Copyright (C) 2021 .
 * @license    __LICENSE__
 */

namespace W3to4;

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\DI\Container;

/**
 * The Ioc class.
 *
 * @since  __DEPLOY_VERSION__
 */
class Ioc
{
    public static ApplicationInterface $rootApp;

    public static AppContext $appContext;

    public static Container $container;

    public static function getApp(): ApplicationInterface
    {
        $container = static::getContainer();

        if ($container->has(AppContext::class)) {
            return $container->get(AppContext::class);
        }

        return $container->get(ApplicationInterface::class);
    }

    /**
     * Method to get property App
     *
     * @return  AppContext
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getAppContext(): AppContext
    {
        return static::$container->get(AppContext::class);
    }

    /**
     * Method to get property Container
     *
     * @return  Container
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getContainer(): Container
    {
        return static::$container;
    }

    /**
     * Method to get property RootApp
     *
     * @return  ApplicationInterface
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getRootApp(): ApplicationInterface
    {
        return static::$rootApp;
    }
}
