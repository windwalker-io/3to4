<?php

/**
 * Part of datavideo project.
 *
 * @copyright  Copyright (C) 2021 .
 * @license    __LICENSE__
 */

namespace W3to4;

use Lyrasoft\Luna\LunaPackage;
use Lyrasoft\Luna\User\UserService;
use Lyrasoft\Unidev\UnidevPackage;
use Lyrasoft\Warder\WarderPackage;
use Phoenix\PhoenixPackage;
use W3to4\Command\EntityCommand;
use W3to4\Command\FormCommand;
use W3to4\Command\MigrationCommand;
use W3to4\Command\RouteCommand;
use W3to4\Command\SeederCommand;
use W3to4\Command\TemplatesCommand;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Events\Web\BeforeAppDispatchEvent;
use Windwalker\Core\Events\Web\BeforeRequestEvent;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Package\AbstractPackage;
use Windwalker\Core\Package\PackageInstaller;
use Windwalker\Core\Renderer\RendererService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\RouteUri;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Driver\ConnectionInterface;
use Windwalker\DI\BootableDeferredProviderInterface;
use Windwalker\DI\BootableProviderInterface;
use Windwalker\DI\Container;
use Windwalker\DI\ServiceProviderInterface;
use Windwalker\Event\Attributes\EventSubscriber;
use Windwalker\Event\Attributes\ListenTo;
use Windwalker\Http\Request\ServerRequest;
use Windwalker\Legacy\Database\DatabaseFactory;
use Windwalker\Legacy\Database\Driver\AbstractDatabaseDriver;
use Windwalker\Legacy\Database\Driver\Mysql\MysqlDriver;
use Windwalker\Legacy\DataMapper\DatabaseContainer;
use Windwalker\Legacy\IO\Input;
use Windwalker\Legacy\IO\PsrInput;

/**
 * The W3to4Package class.
 *
 * @since  __DEPLOY_VERSION__
 */
#[EventSubscriber]
class W3to4Package extends AbstractPackage implements
    ServiceProviderInterface,
    BootableProviderInterface,
    BootableDeferredProviderInterface
{
    /**
     * W3to4Package constructor.
     */
    public function __construct(protected ApplicationInterface $app)
    {
        
    }

    public function install(PackageInstaller $installer): void
    {
        $installer->installConfig(__DIR__ . '/../config/w3to4.php', 'config');
    }

    public function register(Container $container): void
    {
        include_once __DIR__ . '/functions.php';

        if ($this->app->getClient() === ApplicationInterface::CLIENT_CONSOLE) {
            $container->mergeParameters(
                'commands',
                [
                    'w3to4:routes' => RouteCommand::class,
                    'w3to4:mig' => MigrationCommand::class,
                    'w3to4:seed' => SeederCommand::class,
                    'w3to4:entity' => EntityCommand::class,
                    'w3to4:tmpl' => TemplatesCommand::class,
                    'w3to4:form' => FormCommand::class,
                ]
            );

            Ioc::$rootApp = $container->get(ConsoleApplication::class);
            Ioc::$container = $container;
        } else {
            // $container->extend(AppContext::class, function (AppContext $app) {
            //     Ioc::$appContext = $app;
            //     Ioc::$container = $app->getContainer();
            //     return $app;
            // });
        }
        
        $container->extend(RendererService::class, function (RendererService $rendererService, Container $container) {

            $navOptions = RouteUri::MODE_MUTE;

            if ($this->app->isDebug()) {
                $navOptions |= RouteUri::DEBUG_ALERT;
            }

            $nav = $container->get(Navigator::class)
                ->withOptions($navOptions);

            $rendererService->addGlobal('router', $nav);
            $rendererService->addGlobal('user', $container->get(UserService::class)->getCurrentUser());
            $rendererService->addGlobal('datetime', \Windwalker\chronos());
            $rendererService->addGlobal('htmlFrame', $container->get(HtmlFrame::class));

            return $rendererService;
        });

        // DB
        class_alias(AbstractDatabaseDriver::class, \Windwalker\Legacy\Core\Database\DatabaseAdapter::class);

        // Input
        $container->share(
            Input::class,
            function (Container $container) {
                $input = PsrInput::create($request = $container->get(ServerRequest::class));
                $input->merge($input->json->toArray());
                $input->get->merge($input->json->toArray());
                $input->post->merge($input->json->toArray());
                
                return $input;
            }
        )
            ->alias(PsrInput::class, Input::class)
            ->alias('input', PsrInput::class);
    }

    public function boot(Container $container): void
    {
        $container->setOptions(Container::AUTO_WIRE);
    }

    public function bootDeferred(Container $container): void
    {
        // DB
        $db = $container->get(DatabaseAdapter::class);

        $db->getDriver()->useConnection(function (ConnectionInterface $conn) use ($container) {
            $dbo = new MysqlDriver($conn->get());
            $dbo->setDebug($container->getParam('app.debug'));
            DatabaseFactory::setDefaultDbo($dbo);

            $container->share(
                \Windwalker\Legacy\Core\Database\DatabaseAdapter::class,
                $dbo
            );
        });
    }

    #[ListenTo(BeforeAppDispatchEvent::class)]
    public function beforeRequest(BeforeAppDispatchEvent $event): void
    {
        $container = $event->getContainer();

        // Lang
        $lang = $container->get(LangService::class);
        $lang->loadAllFromPath(PhoenixPackage::dir() . '/Resources/language', 'ini');
        $lang->loadAllFromPath(LunaPackage::dir() . '/Resources/language', 'ini');
        $lang->loadAllFromPath(WarderPackage::dir() . '/Resources/language', 'ini');
        $lang->loadAllFromPath(UnidevPackage::dir() . '/Resources/language', 'ini');
    }
}
