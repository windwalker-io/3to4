<?php

/**
 * Part of starter project.
 *
 * @copyright    Copyright (C) 2021 __ORGANIZATION__.
 * @license        __LICENSE__
 */

declare(strict_types=1);

namespace W3to4\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use W3to4\Converter\FileReplacer;
use W3to4\Ioc;
use Windwalker\Console\CommandInterface;
use Windwalker\Console\CommandWrapper;
use Windwalker\Console\IOInterface;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Router\RouteCreator;
use Windwalker\Filesystem\FileObject;
use Windwalker\Scalars\StringObject;
use Windwalker\Utilities\StrInflector;

/**
 * The RouteCommand class.
 */
#[CommandWrapper(
    description: 'Convert routes'
)]
class RouteCommand implements CommandInterface
{
    /**
     * RouteCommand constructor.
     */
    public function __construct(protected ConsoleApplication $app)
    {
    }

    /**
     * configure
     *
     * @param    Command  $command
     *
     * @return    void
     */
    public function configure(Command $command): void
    {
        $command->addArgument(
            'path',
            InputArgument::REQUIRED,
            'Path to replace',
        );
    }

    /**
     * Executes the current command.
     *
     * @param    IOInterface  $io
     *
     * @return    int Return 0 is success, 1-255 is failure.
     */
    public function execute(IOInterface $io): int
    {
        $path = Ioc::getRootApp()->path($io->getArgument('path'));

        $it = FileReplacer::handle(
            $path,
            function (FileObject $file) {
                $parts = explode(DIRECTORY_SEPARATOR, $file->getPath());
                $client = array_pop($parts);
                
                $r = $file->read()
                    ->replaceCallback(
                        '/-\>(\w+)Action\(/',
                        function (array $matches) {
                            return "->{$matches[1]}Handler(";
                        }
                    )
                    ->replaceCallback(
                        '/\/\((\w+)\)/',
                        function (array $matches) {
                            return "/{{$matches[1]}}";
                        }
                    )
                    ->replaceCallback(
                        '/\(\/(\w+)\)/',
                        function (array $matches) {
                            return "[/{{$matches[1]}}]";
                        }
                    )
                    ->replaceCallback(
                        '/controller\(\'(\w+)\'\)/',
                        function (array $matches) use ($client) {
                            $name = ucfirst($matches[1]);
                            $client = ucfirst($client);
                            
                            if (StrInflector::isSingular($name)) {
                                $view = "\App\Module\\{$client}\\{$name}\\{$name}EditView::class";
                            } else {
                                $name = StrInflector::toSingular($name);
                                $view = "\App\Module\\{$client}\\{$name}\\{$name}ListView::class";
                            }

                            $name = StrInflector::toSingular($name);
                            $controller = "\App\Module\\{$client}\\{$name}\\{$name}Controller::class";
                            
                            return "controller($controller)\n->view($view)";
                        }
                    )
                    ->replace('CopyController', 'copy')
                    ->replace('BatchController', 'batch')
                    ->replace('FilterController', 'filter')
                    ->replace(
                        \Windwalker\Legacy\Core\Router\RouteCreator::class,
                        RouteCreator::class,
                    );

                return $r;
            }
        );

        foreach ($it as $file) {
            $io->writeln("[Replace] {$file->getPathname()}");
        }

        return 0;
    }
}
