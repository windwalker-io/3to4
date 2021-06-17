<?php

/**
 * Part of datavideo project.
 *
 * @copyright  Copyright (C) 2021 .
 * @license    __LICENSE__
 */

namespace W3to4\Provider;

use Windwalker\Core\Html\HtmlFrame;
use Windwalker\DI\Container;
use Windwalker\DI\ServiceProviderInterface;

/**
 * The AliasProvider class.
 *
 * @since  __DEPLOY_VERSION__
 */
class AliasProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->alias('html.header', HtmlFrame::class);
    }
}
