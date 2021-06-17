<?php

/**
 * Part of datavideo project.
 *
 * @copyright  Copyright (C) 2021 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

use Windwalker\Core\Language\LangService;

function __(...$args) {
    return \W3to4\Ioc::getContainer()
        ->get(LangService::class)->trans(...$args);
}
