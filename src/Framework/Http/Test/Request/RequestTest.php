<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\Http\Test\Request;

use Windwalker\Legacy\Http\Request\Request;
use Windwalker\Legacy\Uri\PsrUri;

/**
 * Test class of Request
 *
 * @since 2.1
 */
class RequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test instance.
     *
     * @var Request
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->instance = new Request();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
    }

    /**
     * Method to test getHeaders().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Request\Request::getHeaders
     */
    public function testGetHeaders()
    {
        $this->assertEquals([], $this->instance->getHeaders());

        $request = $this->instance->withUri(new PsrUri('http://windwalker.io/flower/sakura'));

        $this->assertEquals(['Host' => ['windwalker.io']], $request->getHeaders());
    }

    /**
     * Method to test getHeader().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Request\Request::getHeader
     */
    public function testGetHeader()
    {
        $this->assertEquals([], $this->instance->getHeader('host'));

        $request = $this->instance->withUri(new PsrUri('http://windwalker.io/flower/sakura'));

        $this->assertEquals(['windwalker.io'], $request->getHeader('host'));
    }

    /**
     * Method to test hasHeader().
     *
     * @return  void
     *
     * @covers \Windwalker\Legacy\Http\Request\Request::hasHeader
     */
    public function testHasHeader()
    {
        $request = new Request('http://example.com/foo', 'GET');

        $this->assertTrue($request->hasHeader('host'));
        $this->assertTrue($request->hasHeader('Host'));
        $this->assertFalse($request->hasHeader('X-Foo'));
    }
}
