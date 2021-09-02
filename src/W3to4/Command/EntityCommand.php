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
use W3to4\Ioc;
use Windwalker\Console\CommandInterface;
use Windwalker\Console\CommandWrapper;
use Windwalker\Console\IOInterface;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Filesystem\Filesystem;
use Windwalker\Utilities\Str;
use Windwalker\Utilities\StrInflector;

/**
 * The EntityCommand class.
 */
#[CommandWrapper(
    description: 'Entity converter.'
)]
class EntityCommand implements CommandInterface
{
    /**
     * EntityCommand constructor.
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

        foreach (Filesystem::files($path) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $name = $file->getBasename('.php');
            $className = Str::removeRight($name, 'Mapper');

            $content = $file->read();

            preg_match('/Table\:\:([\w_]+);/', (string) $content, $match);

            $table = strtoupper($match[1]);
            $alias = StrInflector::toSingular($table);

            $cmd = sprintf(
                'php windwalker g entity %s',
                $className,
            );

            $io->writeln('>> ' . $cmd);

            $this->app->runProcess(
                $cmd,
                null,
                $io->getOutput()
            );
        }

        $cmd = 'php windwalker build:entity "App\\Entity\\*"';

        $io->writeln('>> ' . $cmd);

        $this->app->runProcess(
            $cmd,
            null,
            $io->getOutput()
        );

        // $ref = new \ReflectionClass(Table::class);
        //
        // $constants = $ref->getConstants();
        //
        // foreach ($constants as $name => $constant) {
        //     show($name, $constant);
        // }

        return 0;
    }
}
