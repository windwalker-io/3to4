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
use Windwalker\Core\Migration\Migration;
use Windwalker\Core\Router\RouteCreator;
use Windwalker\Filesystem\FileObject;
use Windwalker\Utilities\StrInflector;
use Windwalker\Utilities\StrNormalize;

/**
 * The MigrationCommand class.
 */
#[CommandWrapper(
    description: 'Convert migration.'
)]
class MigrationCommand implements CommandInterface
{
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
                if ($file->getExtension() !== 'php') {
                    return;
                }

                $r = $file->read()
                    ->replaceCallback(
                        '/\/\*\*[\w\s\d\n\*.\/,:@\{\}]+class\s+(\w+)\s+extends\s+AbstractMigration[\W\w\s\n]+public function up\(\)\n\s+\{/',
                        function (array $matches) use ($file) {
                            return <<<EOF
/**
 * Migration UP: {$file->getBasename('.php')}.
 *
 * @var Migration \$mig
 */
\$mig->up(
    static function () use (\$mig) {
EOF;

                        }
                    )
                    ->replace(
<<<EOL

    /**
     * Migrate Down.
     */
    public function down()
    {
EOL
,
                        <<<REP
);

/**
 * Migration DOWN.
 */
\$mig->down(
    static function () use (\$mig) {
REP
,
                    )
                    ->replaceCallback('/((Warder|Luna)?Table::(\w+))/', function (array $matches) {
                        $name = StrInflector::toSingular(strtolower($matches['3']));
                        return "\\App\\Entity\\" . StrNormalize::toPascalCase($name) . '::class';
                    })
                    ->replace('Data\Data', 'Data\Collection')
                    ->replace('Data()', 'Collection()')
                    ->replace('$this', '$mig')
                    ->replace('->drop(', '->dropTables(')
                    ->replace('->signed(true)', '->unsigned(false)')
                    ->apply(
                        fn ($s) => preg_replace(
                            '/use /',
                            "namespace App\\Migration;\n\nuse ",
                            $s,
                            1
                        )
                    )
                    ->replace(
                        'use Windwalker\Core\Migration\AbstractMigration',
                        'use Windwalker\Core\Migration\Migration'
                    )
                    ->replace('dateTimeThisYear->', 'dateTimeThisYear()->')
                    ->replace('->format($mig->getDateFormat())', '')
                    ->trimRight()
                    ->removeRight('}')
                    ->ensureRight(");\n");

                return $r;
            }
        );

        foreach ($it as $file) {
            $io->writeln("[Replace] {$file->getPathname()}");
        }

        return 0;
    }
}
