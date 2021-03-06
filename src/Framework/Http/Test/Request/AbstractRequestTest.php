<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\Http\Test\Request;

use Windwalker\Legacy\Http\Request\AbstractRequest;
use Windwalker\Legacy\Http\Stream\Stream;
use Windwalker\Legacy\Http\Test\Stub\StubRequest;
use Windwalker\Legacy\Test\TestCase\AbstractBaseTestCase;
use Windwalker\Legacy\Uri\PsrUri;

/**
 * Test class of AbstractRequest
 *
 * @since 2.1
 */
class AbstractRequestTest extends AbstractBaseTestCase
{
    /**
     * Test instance.
     *
     * @var AbstractRequest
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
        $this->instance = new StubRequest();
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
     * testConstruct
     *
     * @return  void
     */
    public function testConstruct()
    {
        // Test no params
        $request = new StubRequest();

        $this->assertInstanceOf('Windwalker\Legacy\Uri\PsrUri', $request->getUri());
        $this->assertEquals('', (string) $request->getUri());
        $this->assertNull($request->getMethod());
        $this->assertInstanceOf('Windwalker\Legacy\Http\Stream\Stream', $request->getBody());
        $this->assertEquals('php://memory', $request->getBody()->getMetadata('uri'));
        $this->assertEquals([], $request->getHeaders());

        // Test with params
        $uri = 'http://example.com/?foo=bar#baz';
        $method = 'post';
        $body = fopen($tmpfile = tempnam(sys_get_temp_dir(), 'windwalker'), 'wb+');
        $headers = [
            'X-Foo' => ['Flower', 'Sakura'],
            'Content-Type' => 'application/json',
        ];

        $request = new StubRequest($uri, $method, $body, $headers);

        $this->assertInstanceOf('Windwalker\Legacy\Uri\PsrUri', $request->getUri());
        $this->assertEquals('http://example.com/?foo=bar#baz', (string) $request->getUri());
        $this->assertEquals('POST', $request->getMethod());
        $this->assertInstanceOf('Windwalker\Legacy\Http\Stream\Stream', $request->getBody());
        $this->assertEquals($tmpfile, $request->getBody()->getMetadata('uri'));
        $this->assertEquals(['Flower', 'Sakura'], $request->getHeader('x-foo'));
        $this->assertEquals(['application/json'], $request->getHeader('content-type'));

        fclose($body);

        // Test with object params
        $uri = new PsrUri('http://example.com/flower/sakura?foo=bar#baz');
        $body = new Stream();
        $request = new StubRequest($uri, null, $body);

        $this->assertSame($uri, $request->getUri());
        $this->assertSame($body, $request->getBody());
    }

    /**
     * Method to test getRequestTarget().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Request\AbstractRequest::getRequestTarget
     * @covers \Windwalker\Legacy\Http\Request\AbstractRequest::withRequestTarget
     */
    public function testWithAndGetRequestTarget()
    {
        $this->assertEquals('/', $this->instance->getRequestTarget());

        $request = $this->instance->withUri(new PsrUri('http://example.com/flower/sakura?foo=bar#baz'));

        $this->assertNotSame($request, $this->instance);
        $this->assertEquals('/flower/sakura?foo=bar', (string) $request->getRequestTarget());

        $request = $request->withUri(new PsrUri('http://example.com'));

        $this->assertEquals('/', (string) $request->getRequestTarget());

        $request = $request->withRequestTarget('*');

        $this->assertEquals('*', $request->getRequestTarget());
    }

    /**
     * Method to test getMethod().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Request\AbstractRequest::getMethod
     * @covers \Windwalker\Legacy\Http\Request\AbstractRequest::withMethod
     */
    public function testWithAndGetMethod()
    {
        $this->assertNull($this->instance->getMethod());

        $request = $this->instance->withMethod('patch');

        $this->assertNotSame($request, $this->instance);
        $this->assertEquals('PATCH', $request->getMethod());

        $this->assertExpectedException(
            function () use ($request) {
                $request->withMethod('FLY');
            },
            new \InvalidArgumentException()
        );
    }

    /**
     * Method to test getUri().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Request\AbstractRequest::getUri
     * @covers \Windwalker\Legacy\Http\Request\AbstractRequest::withUri
     */
    public function testWithAndGetUri()
    {
        $this->assertInstanceOf('Windwalker\Legacy\Uri\PsrUri', $this->instance->getUri());
        $this->assertEquals('', (string) $this->instance->getUri());

        $request = $this->instance->withUri(new PsrUri('http://example.com/flower/sakura?foo=bar#baz'), true);

        $this->assertNotSame($request, $this->instance);
        $this->assertEquals('http://example.com/flower/sakura?foo=bar#baz', (string) $request->getUri());
        $this->assertEquals([], $request->getHeader('host'));

        $request = $this->instance->withUri(new PsrUri('http://windwalker.io/flower/sakura?foo=bar#baz'));

        $this->assertEquals('http://windwalker.io/flower/sakura?foo=bar#baz', (string) $request->getUri());
        $this->assertEquals(['windwalker.io'], $request->getHeader('host'));
    }
}
