<?php

/**
 * Part of datavideo project.
 *
 * @copyright  Copyright (C) 2021 .
 * @license    __LICENSE__
 */

namespace W3to4\Marco;

use Windwalker\Utilities\Classes\MarcoableTrait;

/**
 * MethodMappingMarcoTrait
 *
 * @since  {DEPLOY_VERSION}
 */
trait MethodProxiesMarcoTrait
{
    use MarcoableTrait;

    public static function addMethodProxy(string $name, string|callable $proxy): void
    {
        if (is_string($proxy)) {
            static::macro($name, function (...$args) use ($proxy) {
                return $this->$proxy(...$args);
            });
        } else {
            static::macro($name, $proxy);
        }
    }

    public static function addMethodProxies(array $proxies): void
    {
        foreach ($proxies as $name => $proxy) {
            static::addMethodProxy($name, $proxy);
        }
    }
}
