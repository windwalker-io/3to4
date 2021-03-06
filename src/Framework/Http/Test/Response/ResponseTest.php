<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\Http\Test\Response;

use Windwalker\Legacy\Http\Response\Response;
use Windwalker\Legacy\Http\Stream\Stream;

/**
 * Test class of Response
 *
 * @since 2.1
 */
class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test instance.
     *
     * @var Response
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
        $this->instance = new Response();
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

    public function testConstruct()
    {
        // Test no params
        $res = new Response();

        $this->assertInstanceOf('Windwalker\Legacy\Http\Stream\Stream', $res->getBody());
        $this->assertEquals('php://memory', $res->getBody()->getMetadata('uri'));
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertEquals([], $res->getHeaders());

        // Test with params
        $body = fopen($tmpfile = tempnam(sys_get_temp_dir(), 'windwalker'), 'wb+');
        $headers = [
            'X-Foo' => ['Flower', 'Sakura'],
            'Content-Type' => 'application/json',
        ];

        $res = new Response($body, 404, $headers);

        $this->assertInstanceOf('Windwalker\Legacy\Http\Stream\Stream', $res->getBody());
        $this->assertEquals($tmpfile, $res->getBody()->getMetadata('uri'));
        $this->assertEquals(['Flower', 'Sakura'], $res->getHeader('x-foo'));
        $this->assertEquals(['application/json'], $res->getHeader('content-type'));

        fclose($body);

        // Test with object params
        $body = new Stream();
        $res = new Response($body);

        $this->assertSame($body, $res->getBody());
    }

    /**
     * Method to test getStatusCode().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Response\Response::getStatusCode()
     * @covers \Windwalker\Legacy\Http\Response\Response::withStatus
     */
    public function testWithAndGetStatusCode()
    {
        $this->assertEquals(200, $this->instance->getStatusCode());

        $res = $this->instance->withStatus(403);

        $this->assertNotSame($res, $this->instance);
        $this->assertEquals(403, $res->getStatusCode());

        $res = $res->withStatus(500, 'Unknown error');

        $this->assertEquals(500, $res->getStatusCode());
        $this->assertEquals('Unknown error', $res->getReasonPhrase());
    }

    /**
     * Method to test getReasonPhrase().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Response\Response::getReasonPhrase
     */
    public function testGetReasonPhrase()
    {
        $res = new Response();

        $res = $res->withStatus(200);

        $this->assertEquals('OK', $res->getReasonPhrase());

        $res = $res->withStatus(400);

        $this->assertEquals('Bad Request', $res->getReasonPhrase());

        $res = $res->withStatus(404);

        $this->assertEquals('Not Found', $res->getReasonPhrase());

        $res = $res->withStatus(500);

        $this->assertEquals('Internal Server Error', $res->getReasonPhrase());
    }
}
