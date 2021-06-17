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
use Symfony\Component\Console\Input\InputOption;
use W3to4\Converter\FileReplacer;
use W3to4\Ioc;
use Windwalker\Console\CommandInterface;
use Windwalker\Console\CommandWrapper;
use Windwalker\Console\IOInterface;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Router\RouteCreator;
use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Path;
use Windwalker\Form\FieldDefinitionInterface;
use Windwalker\Form\Form;
use Windwalker\Legacy\Core\Form\AbstractFieldDefinition;
use Windwalker\Scalars\StringObject;

use Windwalker\Utilities\Str;
use Windwalker\Utilities\StrInflector;
use Windwalker\Utilities\StrNormalize;

use function Windwalker\collect;

/**
 * The RouteCommand class.
 */
#[CommandWrapper(
    description: 'Convert form definition'
)]
class FormCommand implements CommandInterface
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

        // $command->addOption(
        //     'type',
        //     't',
        //     InputOption::VALUE_REQUIRED,
        //     'List or Edit',
        // );
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
        // $type = $io->getOption('type');

        $it = FileReplacer::handle(
            $path . '/**/*Definition.php',
            function (FileObject $file) {
                $r = $file->read()
                    ->replaceCallback(
                        '/namespace\s+([\w]+)\\\\Form\\\\(\w+);/',
                        function (array $matches) use ($file) {
                            $name = StrInflector::toSingular($matches[2]);
                            return "namespace App\\Module\\{$matches[1]}\\{$name}\\Form;";
                        }
                    )
                    ->replace(
                        'Definition extends AbstractFieldDefinition',
                        'Form implements FieldDefinitionInterface'
                    )
                    ->replace(
                        \Windwalker\Legacy\Form\Form::class,
                        Form::class
                    )
                    ->replace(
                        AbstractFieldDefinition::class,
                        FieldDefinitionInterface::class
                    )
                    ->replaceCallback(
                        '/(protected|public)\s+function\s+doDefine\((.*)\)/',
                        function () {
                            return "public function define(Form \$form): void";
                        }
                    )
                    ->replace('$this->', '$form->')
                    ->replace('static::TEXT_MAX_UTF8', '21844')
                    ->replaceCallback(
                        "/\\\$form->(\w+)\('([\w.\-_]+)'\)/",
                        function (array $matches) {
                            $class = ucfirst($matches[1]) . 'Field::class';
                            $coreClassName = '\Windwalker\Form\Field\\' . ucfirst($matches[1]) . 'Field';

                            if (class_exists($coreClassName)) {
                                $class = $coreClassName . '::class';
                            }

                            return "\$form->add('{$matches[2]}', {$class})";
                        }
                    )
                    ->replace('SwitchField', 'SwitcherField')
                    ->replace('->required()', '->required(true)')
                    ->replace('->disabled()', '->disabled(true)');

                return [
                    $this->getDest($file),
                    $r
                ];
            }
        );

        foreach ($it as $file) {
            $io->writeln("[Replace] {$file->getPathname()}");
        }

        return 0;
    }

    protected function getDest(FileObject $file): string
    {
        $basename = $file->getBasename('.php');
        $paths = \Windwalker\str(Path::normalize($file->getPath(), '/'))
            ->explode('/');
        $formName = $paths->pop();
        $paths->pop();
        $clientName = $paths->pop();
        
        $fileName = Str::removeRight($basename, 'Definition');

        return sprintf(
            "%s/Module/%s/%s/Form/%sForm.php",
            WINDWALKER_SOURCE,
            $clientName,
            StrInflector::toSingular($formName),
            $fileName
        );
    }
}
