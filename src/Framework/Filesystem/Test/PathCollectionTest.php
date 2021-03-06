<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Filesystem\Test;

use Windwalker\Legacy\Filesystem\Path;
use Windwalker\Legacy\Filesystem\Path\PathCollection;
use Windwalker\Legacy\Filesystem\Path\PathLocator;

/**
 * Tests for the PathCollection class.
 *
 * @since  2.0
 * @deprecated Legacy code
 */
class PathCollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Property collection.
     *
     * @var PathCollection
     */
    public $collection;

    /**
     * setUp description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  setUpReturn
     *
     * @since  2.0
     */
    public function setUp(): void
    {
        $this->collection = new PathCollection();
    }

    /**
     * Data provider for testClean() method.
     *
     * @return  array
     *
     * @since   2.0
     */
    public function getPathData()
    {
        return [
            // Input Path, Directory Separator, Expected Output
            'one path' => [
                '/var/www/foo/bar',
                [new PathLocator('/var/www/foo/bar')],
            ],

            'paths with on key' => [
                [
                    '/',
                    '/var/www/foo/bar',
                    '/var/www/windwalker/bar/foo',
                ],
                [
                    new PathLocator('/'),
                    new PathLocator('/var/www/foo/bar'),
                    new PathLocator('/var/www/windwalker/bar/foo'),
                ],
            ],

            'paths with key' => [
                [
                    'root' => '/',
                    'foo' => '/var/www/foo',
                ],
                [
                    'root' => new PathLocator('/'),
                    'foo' => new PathLocator('/var/www/foo'),
                ],
            ],
        ];
    }

    /**
     * Data provider for testClean() method.
     *
     * @return  array
     *
     * @since   2.0
     */
    public function getIteratorData()
    {
        return [
            'no rescurive' => [
                [
                    __DIR__ . '/files/folder1',
                    __DIR__ . '/files/folder2',
                ],

                [
                    Path::clean(__DIR__ . '/files/folder1'),
                    Path::clean(__DIR__ . '/files/folder2'),
                ],

                false,
            ],
            /*
            'rescurive' => array(
                array(
                    __DIR__ . 'files'
                ),

                array(
                    Path::clean(__DIR__ . 'files/folder1'),
                    Path::clean(__DIR__ . 'files/folder1/file1'),
                    Path::clean(__DIR__ . 'files/folder2/file2.html'),
                    Path::clean(__DIR__ . 'files/file2.txt')
                ),

                true
            )
            */
        ];
    }

    /**
     * name description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  nameReturn
     *
     * @since  2.0
     */
    public function getIteratorRecursiveData()
    {
        return [
            'no rescurive' => [
                [
                    __DIR__ . '/files/folder1',
                    __DIR__ . '/files/folder2',
                    __DIR__ . '/files/file2.txt',
                ],

                [
                    Path::clean(__DIR__ . '/files/folder1'),
                    Path::clean(__DIR__ . '/files/folder2'),
                ],

                false,
            ],

            'rescurive' => [
                [
                    __DIR__ . '/files',
                ],

                [
                    Path::clean(__DIR__ . '/files/folder1'),
                    Path::clean(__DIR__ . '/files/folder1/path1'),
                    Path::clean(__DIR__ . '/files/folder2/file2.html'),
                    Path::clean(__DIR__ . '/files/file2.txt'),
                ],

                true,
            ],
        ];
    }

    /**
     * test__construct description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  test__constructReturn
     *
     * @since  2.0
     */
    public function testConstruct()
    {
        $collections = new PathCollection('/var/www/foo/bar');

        $paths = $collections->getPaths();

        $this->assertEquals([new PathLocator('/var/www/foo/bar')], $paths);
    }

    /**
     * testAddPaths description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  testAddPathsReturn
     *
     * @dataProvider  getPathData
     *
     * @since         2.0
     */
    public function testAddPaths($paths, $expects)
    {
        $this->collection->addPaths($paths);

        $paths = $this->collection->getPaths();

        $this->assertEquals($paths, $expects);
    }

    /**
     * addPath description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  addPathReturn
     *
     * @since  2.0
     */
    public function testAddPath()
    {
        $path = new PathLocator('/var/foo/bar');

        $this->collection->addPath($path, 'bar');

        $this->assertEquals($path, $this->collection->getPath('bar'));
    }

    /**
     * removePath description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  removePathReturn
     *
     * @since  2.0
     */
    public function testRemovePath()
    {
        $path = new PathLocator('/var/foo/bar');

        $this->collection->addPath($path, 'bar');

        $this->collection->removePath('bar');

        $path = $this->collection->getPath('bar');

        $this->assertNull($path);
    }

    /**
     * getPaths description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getPathsReturn
     *
     * @dataProvider  getPathData
     *
     * @since         2.0
     */
    public function testGetPaths($paths, $expects)
    {
        $this->setUp();

        $this->collection->addPaths($paths);

        $paths = $this->collection->getPaths();

        $this->assertEquals($paths, $expects);
    }

    /**
     * getPath description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getPathReturn
     *
     * @since  2.0
     */
    public function testGetPath()
    {
        $path = new PathLocator('/var/foo/bar2');

        $this->collection->addPath($path, 'bar2');

        $this->assertEquals($path, $this->collection->getPath('bar2'));

        $this->assertEquals(new PathLocator('/'), $this->collection->getPath('bar3', '/'));
    }

    /**
     * getIterator description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getIteratorReturn
     *
     * @dataProvider  getIteratorData
     *
     * @since         2.0
     */
    public function testGetIterator($paths, $expects, $rescursive)
    {
        $this->setUp();

        $this->collection->addPaths($paths);

        $iterator = $this->collection;

        $compare = [];

        foreach ($iterator as $file) {
            $compare[] = (string) $file;
        }

        $this->assertEquals($compare, $expects);
    }

    /**
     * testDiresctoryIterator description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  testDiresctoryIteratorReturn
     *
     * @since  2.0
     */
    public function testGetDiresctoryIterator()
    {
        $this->markTestIncomplete();
    }

    /**
     * setPrefix description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  setPrefixReturn
     *
     * @since  2.0
     */
    public function testSetPrefix()
    {
        $this->setUp();

        $this->collection->addPath('windwalker/dir/foo/bar', 'foo');
        $this->collection->addPath('windwalker/dir/yoo/hoo', 'yoo');

        $this->collection->setPrefix('/var/www');

        $expects = [
            Path::clean('/var/www/windwalker/dir/foo/bar'),
            Path::clean('/var/www/windwalker/dir/yoo/hoo'),
        ];

        $paths = [
            (string) $this->collection->getPath('foo'),
            (string) $this->collection->getPath('yoo'),
        ];

        $this->assertEquals($paths, $expects);
    }

    /**
     * find description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  findReturn
     *
     * @since  2.0
     */
    public function find()
    {
    }

    /**
     * findAll description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  findAllReturn
     *
     * @since  2.0
     */
    public function findAll()
    {
    }

    /**
     * toArray description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  toArrayReturn
     *
     * @since  2.0
     */
    public function toArray()
    {
    }

    /**
     * getFiles description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getFilesReturn
     *
     * @since  2.0
     */
    public function getFiles($rescursive = false)
    {
    }

    /**
     * getFolders description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getFoldersReturn
     *
     * @since  2.0
     */
    public function getFolders($rescursive)
    {
    }

    /**
     * appendAll description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  appendAllReturn
     *
     * @since  2.0
     */
    public function appendAll()
    {
    }

    /**
     * prependAll description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  prependAllReturn
     *
     * @since  2.0
     */
    public function prependAll()
    {
    }
}
