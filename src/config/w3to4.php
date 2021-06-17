<?php

/**
 * Part of datavideo project.
 *
 * @copyright  Copyright (C) 2021 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

use Lyrasoft\Unidev\Provider\UnidevProvider;
use W3to4\Ioc;
use W3to4\W3to4Package;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Events\Web\BeforeAppDispatchEvent;
use Windwalker\Core\Events\Web\BeforeRequestEvent;
use Windwalker\Event\Attributes\ListenTo;

return [
    'w3to4' => [
        'providers' => [
            \W3to4\W3to4Package::class,
            UnidevProvider::class
        ],
        'binding' => [

        ],
        'aliases' => [
            'html.header' => \Windwalker\Core\Html\HtmlFrame::class,
            'database' => \Windwalker\Legacy\Core\Database\DatabaseAdapter::class,
            'user.manager' => \Lyrasoft\Luna\User\UserService::class
        ],
        'layouts' => [
            //
        ],
        'listeners' => [
            AppContext::class => [
                W3to4Package::class,
                BeforeAppDispatchEvent::class => function (BeforeAppDispatchEvent $event) {
                    Ioc::$container  = $event->getContainer();
                    Ioc::$appContext = $event->getContainer()->get(AppContext::class);
                },
            ],
        ]
    ]
];
