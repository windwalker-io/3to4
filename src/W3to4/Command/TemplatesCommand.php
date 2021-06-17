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
use Windwalker\Scalars\StringObject;

use Windwalker\Utilities\StrInflector;
use Windwalker\Utilities\StrNormalize;

use function Windwalker\collect;

/**
 * The RouteCommand class.
 */
#[CommandWrapper(
    description: 'Convert view templates'
)]
class TemplatesCommand implements CommandInterface
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

        $command->addOption(
            'type',
            't',
            InputOption::VALUE_REQUIRED,
            'List or Edit',
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
        $type = $io->getOption('type');

        $it = FileReplacer::handle(
            $path . '/**/*.blade.php',
            function (FileObject $file) use ($type) {
                $r = $file->read()
                    ->replace("@section('admin-body')", "@section('content')")
                    ->replace('@translate', '@lang')
                    ->replace('$warder->extends', '\'admin.global.body\'')
                    ->replace('$luna->extends', '\'admin.global.body\'')
                    ->pipeIf(
                        $type === 'list',
                        fn (StringObject $s) => $s->replace('<form ', '<form x-data="{ grid: $store.grid }" x-ref="gridForm" data-ordering="{{ $ordering }}" ')
                    )
                    ->replace('{!! $grid->checkbox() !!}', '<x-row-checkbox :row="$i" :id="$item->id"></x-row-checkbox>')
                    ->replace('Windwalker\Legacy\Core\DateTime\Chronos::toLocalTime', '$chronos->toLocalFormat')
                    ->replace('$pagination->route($view->name, [])->render()', '$pagination->render()')
                    ->replaceCallback(
                        "/{!!\s+\\\$filterBar->render\(.*?\)\s!!}/",
                        function (array $matches) {
                            return '<x-filter-bar :form="$form" :open="$showFilters"></x-filter-bar>';
                        }
                    )
                    ->replaceCallback(
                        "/{!!\s+\\\$grid->checkboxesToggle\(.*\)\s+!!}/",
                        function (array $matches) {
                            return '<x-toggle-all></x-toggle-all>';
                        }
                    )
                    ->replaceCallback(
                        "/{!!\s+\\\$grid->sortTitle\('([\w.]+)',\s+'([\w.]+)'\)\s!!}/",
                        function (array $matches) {
                            return "<x-sort field=\"{$matches[2]}\" >@lang('{$matches[1]}')</x-sort>";
                        }
                    )
                    ->replace(
                        'Phoenix.Grid.updateRow({{ $i }}',
                        'grid.updateRow({{ $item->id }}'
                    )
                    ->replace(
                        'Phoenix.Grid.deleteRow({{ $i }}',
                        'grid.deleteRow({{ $item->id }}'
                    )
                    ->replaceCallback(
                        "/Phoenix.Grid.doTask\('(\w+)',\s+{{\s+\\\$i\s+}}/",
                        function (array $matches) {
                            return "grid.doTask('{$matches[1]}', {{ \$item->id }}";
                        }
                    )
                    ->replace(
                        'Phoenix.Grid.',
                        'grid.'
                    )
                    ->replace(
                        'Phoenix.Form.',
                        'form.'
                    );

                return [
                    $this->getDest($file, $type),
                    $r
                ];
            }
        );

        foreach ($it as $file) {
            $io->writeln("[Replace] {$file->getPathname()}");
        }

        return 0;
    }

    protected function getDest(FileObject $file, string $type): string
    {
        $basename = $file->getBasename('.blade.php');
        $viewName = \Windwalker\str(Path::normalize($file->getPath(), '/'))
            ->explode('/')
            ->pop();

        if (str_contains($file->getPathname(), 'Front')) {
            // Front
            if ($type === 'list') {
                $destFilename = match ($basename) {
                    default => StrInflector::toSingular($basename) . '-list'
                };
            } elseif ($type === 'item') {
                $destFilename = match ($basename) {
                    default => $basename . '-item'
                };
            } else {
                $destFilename = $basename;
            }

            $dest = WINDWALKER_SOURCE . '/Module/Front/' . StrNormalize::toPascalCase(StrInflector::toSingular($viewName)) . '/views';
        } else {
            // Admin
            if ($type === 'list') {
                $destFilename = match ($basename) {
                    'toolbar' => 'list-toolbar',
                    default => StrInflector::toSingular($basename) . '-list'
                };
            } elseif ($type === 'edit') {
                $destFilename = match ($basename) {
                    'toolbar' => 'edit-toolbar',
                    default => $basename . '-edit'
                };
            } else {
                $destFilename = $basename;
            }

            $dest = WINDWALKER_SOURCE . '/Module/Admin/' . StrNormalize::toPascalCase(StrInflector::toSingular($viewName)) . '/views';
        }

        $destFilename .= '.blade.php';

        return $dest . '/' . $destFilename;
    }
}
