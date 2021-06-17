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
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Filesystem;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\StrInflector;
use Windwalker\Utilities\StrNormalize;

/**
 * The SeederCommand class.
 */
#[CommandWrapper(
    description: ''
)]
class SeederCommand implements CommandInterface
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
        
        $mainSeeder = $path . '/MainSeeder.php';
        $mainSeederContent = file_get_contents($mainSeeder);
        
        preg_match_all('/\$this\-\>execute\((\w+)::class\);/', $mainSeederContent, $matches);
        
        Filesystem::delete($mainSeeder);
        
        $content = '';

        foreach ($matches[1] as $seeder) {
            $name = StrNormalize::toKebabCase($seeder);
            $content .= "    __DIR__ . '/$name.php',\n";
        }
        
        $content = <<<PHP
<?php

return [
$content
];
PHP;
        
        Filesystem::write($path . '/main.php', $content);

        $it = FileReplacer::handle(
            $path,
            function (FileObject $file) {
                if ($file->getBasename('.php') === 'main') {
                    return;
                }

                if ($file->getExtension() !== 'php') {
                    return;
                }

                $r = $file->read()
                    ->replaceCallback(
                        '/\/\*\*[\w\s\d\n\*.\/,:@\{\}]+class\s+(\w+)\s+extends\s+AbstractSeeder[\W\w\s\n]+public function doExecute\(\)\n\s+\{/',
                        function (array $matches) use ($file) {
                            return <<<EOF
/**
 * {$file->getBasename('.php')}
 *
 * @var Seeder          \$seeder
 * @var ORM             \$orm
 * @var DatabaseAdapter \$db
 */
\$seeder->import(
    static function () use (\$seeder, \$orm, \$db) {
EOF;

                        }
                    )
                    ->replace(
                        <<<EOL
    /**
     * doClear
     *
     * @return  void
     */
    public function doClear()
    {
EOL
                        ,
                        <<<REP
);

\$seeder->clear(
    static function () use (\$seeder, \$orm, \$db) {
REP
                        ,
                    )
                    ->replace('Data\Data', 'Data\Collection')
                    ->replace('Data()', 'Collection()')
                    ->replace('$this', '$seeder')
                    ->replaceCallback('/((Warder|Luna)?Table::(\w+))/', function (array $matches) {
                        $name = StrInflector::toSingular(strtolower($matches['3']));
                        return "\\App\\Entity\\" . StrNormalize::toPascalCase($name) . '::class';
                    })
                    ->apply(
                        fn ($s) => preg_replace(
                            '/use /',
                            "namespace App\\Seeder;\n\nuse ",
                            $s,
                            1
                        )
                    )
                    ->replace(
                        'use Windwalker\Core\Seeder\AbstractSeeder',
                        'use Windwalker\Core\Seed\Seeder'
                    )
                    ->pipe(fn ($s) => FileReplacer::addUse($s, DatabaseAdapter::class))
                    ->pipe(fn ($s) => FileReplacer::addUse($s, ORM::class))
                    ->replace('->format($seeder->getDateFormat())', '')
                    ->replace('$seeder->faker->create', '$seeder->faker')
                    ->trimRight()
                    ->removeRight('}')
                    ->ensureRight(");\n");

                $file->delete();

                return [
                    $file->getPath() . '/' . StrNormalize::toKebabCase($file->getBasename('.php')) . '.php',
                    $r
                ];
            }
        );

        foreach ($it as $file) {
            $io->writeln("[Replace] {$file->getPathname()}");
        }

        return 0;
    }
}
