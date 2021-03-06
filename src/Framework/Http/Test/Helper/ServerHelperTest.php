<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\Http\Test\Helper;

use Windwalker\Legacy\Http\Helper\ServerHelper;
use Windwalker\Legacy\Http\UploadedFile;

/**
 * Test class of ServerHelper
 *
 * @since 3.0
 */
class ServerHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Method to test getValue().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Helper\ServerHelper::getValue
     */
    public function testGetValue()
    {
        $servers = [
            'HTTP_FOO' => 'foo',
            'X_BAR' => 'bar',
            'CONTENT_BAZ' => ['baz'],
        ];

        $this->assertEquals(ServerHelper::getValue($servers, 'HTTP_FOO'), 'foo');
        $this->assertEquals(ServerHelper::getValue($servers, 'HTTP_BAR'), null);
        $this->assertEquals(ServerHelper::getValue($servers, 'x_bar'), null);
        $this->assertEquals(ServerHelper::getValue($servers, 'x_bar', 'default'), 'default');
    }

    /**
     * Method to test validateUploadedFiles().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Helper\ServerHelper::validateUploadedFiles
     */
    public function testValidateUploadedFiles()
    {
        $files = [new UploadedFile('php://memory')];

        $this->assertTrue(ServerHelper::validateUploadedFiles($files));

        // Nested array
        $files[] = $files;

        $this->assertTrue(ServerHelper::validateUploadedFiles($files));
    }

    /**
     * Method to test getAllHeaders().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Helper\ServerHelper::getAllHeaders
     * @TODO   Implement testGetAllHeaders().
     */
    public function testGetAllHeaders()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test apacheRequestHeaders().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Helper\ServerHelper::apacheRequestHeaders
     * @TODO   Implement testApacheRequestHeaders().
     */
    public function testApacheRequestHeaders()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * testParseFormData
     *
     * @return  void
     *
     * @covers \Windwalker\Legacy\Http\Helper\ServerHelper::parseFormData
     */
    public function testParseFormData()
    {
        $type = 'multipart/form-data; boundary=----WebKitFormBoundary8zi5vcW6H9OgqKSj';

        $input = <<<DATA
------WebKitFormBoundary8zi5vcW6H9OgqKSj
Content-Disposition: form-data; name="flower"

SAKURA
------WebKitFormBoundary8zi5vcW6H9OgqKSj
Content-Disposition: form-data; name="tree"

Marabutan
------WebKitFormBoundary8zi5vcW6H9OgqKSj
Content-Disposition: form-data; name="fruit"

Apple
------WebKitFormBoundary8zi5vcW6H9OgqKSj--
DATA;

        $input = str_replace("\r\n", "\n", $input);
        $input = str_replace("\n", "\r\n", $input);

        $this->assertEquals(
            [
                'data' => ['flower' => 'SAKURA', 'tree' => 'Marabutan', 'fruit' => 'Apple'],
                'files' => [],
            ],
            ServerHelper::parseFormData($input)
        );
    }
}
