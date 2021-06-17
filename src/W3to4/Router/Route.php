<?php

/**
 * Part of datavideo project.
 *
 * @copyright  Copyright (C) 2021 .
 * @license    __LICENSE__
 */

namespace W3to4\Router;

/**
 * The RouteCreator class.
 *
 * @since  __DEPLOY_VERSION__
 */
class Route extends \Windwalker\Core\Router\Route
{
    public function __call(string $name, array $args = [])
    {
        return match($name) {
            'getAction' => $this->getHandler(...$args),
            'postAction' => $this->postHandler(...$args),
            'putAction' => $this->putHandler(...$args),
            'patchAction' => $this->patchHandler(...$args),
            'deleteAction' => $this->deleteHandler(...$args),
            default => parent::__call($name, $args)
        };
    }
}
