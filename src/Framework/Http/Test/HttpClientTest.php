<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\Http\Test;

use Windwalker\Legacy\Http\HttpClient;
use Windwalker\Legacy\Http\Request\Request;
use Windwalker\Legacy\Http\Test\Mock\MockTransport;
use Windwalker\Legacy\Uri\Uri;
use Windwalker\Legacy\Uri\UriHelper;

/**
 * Test class of HttpClient
 *
 * @since 2.1
 */
class HttpClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test instance.
     *
     * @var HttpClient
     */
    protected $instance;

    /**
     * Property mock.
     *
     * @var  MockTransport
     */
    protected $transport;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->instance = $this->createClient();
    }

    /**
     * createClient
     *
     * @param array $options
     * @param null  $transport
     *
     * @return  HttpClient
     */
    protected function createClient($options = [], $transport = null)
    {
        $this->transport = $transport = $transport ?: new MockTransport();

        return new HttpClient($options, $transport);
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
     * testDownload
     *
     * @return  void
     */
    public function testDownload()
    {
        $url = 'http://example.com';
        $dest = '/path/to/file';

        $this->instance->download($url, $dest);

        $this->assertEquals('GET', $this->transport->request->getMethod());
        $this->assertEquals('http://example.com', $this->transport->request->getRequestTarget());
        $this->assertEquals('/path/to/file', $this->transport->getOption('target_file'));
    }

    /**
     * Method to test request().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::request
     */
    public function testRequest()
    {
        $url = new Uri('http://example.com/?foo=bar');

        $this->instance->request('GET', $url, ['flower' => 'sakura'], ['X-Foo' => 'Bar']);

        $this->assertEquals('GET', $this->transport->request->getMethod());
        $this->assertEquals('http://example.com/?foo=bar&flower=sakura', $this->transport->request->getRequestTarget());
        $this->assertEquals('', $this->transport->request->getBody()->__toString());
        $this->assertEquals(['X-Foo' => ['Bar']], $this->transport->request->getHeaders());

        $url = new Uri('http://example.com/?foo=bar');

        $this->instance->request('POST', $url, ['flower' => 'sakura'], ['X-Foo' => 'Bar']);

        $this->assertEquals('POST', $this->transport->request->getMethod());
        $this->assertEquals('http://example.com/?foo=bar', $this->transport->request->getRequestTarget());
        $this->assertEquals('flower=sakura', $this->transport->request->getBody()->__toString());
        $this->assertEquals(['X-Foo' => ['Bar']], $this->transport->request->getHeaders());
    }

    /**
     * Method to test send().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::send
     */
    public function testSend()
    {
        $request = new Request();

        $this->instance->send($request);

        $this->assertSame($request, $this->transport->request);
    }

    /**
     * Method to test options().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::options
     */
    public function testOptions()
    {
        $url = 'http://example.com/?foo=bar';
        $headers = ['X-Foo' => 'Bar'];

        $this->instance->options($url, $headers);

        $this->assertEquals('OPTIONS', $this->transport->request->getMethod());
        $this->assertEquals($url, $this->transport->request->getRequestTarget());
        $this->assertEquals('Bar', $this->transport->request->getHeaderLine('X-Foo'));
    }

    /**
     * Method to test head().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::head
     */
    public function testHead()
    {
        $url = 'http://example.com/?foo=bar';
        $headers = ['X-Foo' => 'Bar'];

        $this->instance->head($url, $headers);

        $this->assertEquals('HEAD', $this->transport->request->getMethod());
        $this->assertEquals($url, $this->transport->request->getRequestTarget());
        $this->assertEquals('Bar', $this->transport->request->getHeaderLine('X-Foo'));
    }

    /**
     * Method to test get().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::get
     */
    public function testGet()
    {
        $url = new Uri('http://example.com/?foo=bar');

        $this->instance->get($url, ['flower' => 'sakura'], ['X-Foo' => 'Bar']);

        $this->assertEquals('GET', $this->transport->request->getMethod());
        $this->assertEquals('http://example.com/?foo=bar&flower=sakura', $this->transport->request->getRequestTarget());
        $this->assertEquals('', $this->transport->request->getBody()->__toString());
        $this->assertEquals(['X-Foo' => ['Bar']], $this->transport->request->getHeaders());
    }

    /**
     * Method to test post().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::post
     */
    public function testPost()
    {
        $url = 'http://example.com/?foo=bar';
        $data = ['flower' => 'sakura'];
        $headers = ['X-Foo' => 'Bar'];

        $this->instance->post($url, $data, $headers);

        $this->assertEquals('POST', $this->transport->request->getMethod());
        $this->assertEquals($url, $this->transport->request->getRequestTarget());
        $this->assertEquals('Bar', $this->transport->request->getHeaderLine('X-Foo'));
        $this->assertEquals(UriHelper::buildQuery($data), $this->transport->request->getBody()->__toString());
    }

    /**
     * Method to test put().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::put
     */
    public function testPut()
    {
        $url = 'http://example.com/?foo=bar';
        $data = ['flower' => 'sakura'];
        $headers = ['X-Foo' => 'Bar'];

        $this->instance->put($url, $data, $headers);

        $this->assertEquals('PUT', $this->transport->request->getMethod());
        $this->assertEquals($url, $this->transport->request->getRequestTarget());
        $this->assertEquals('Bar', $this->transport->request->getHeaderLine('X-Foo'));
        $this->assertEquals(UriHelper::buildQuery($data), $this->transport->request->getBody()->__toString());
    }

    /**
     * Method to test delete().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::delete
     */
    public function testDelete()
    {
        $url = 'http://example.com/?foo=bar';
        $data = ['flower' => 'sakura'];
        $headers = ['X-Foo' => 'Bar'];

        $this->instance->delete($url, $data, $headers);

        $this->assertEquals('DELETE', $this->transport->request->getMethod());
        $this->assertEquals($url, $this->transport->request->getRequestTarget());
        $this->assertEquals('Bar', $this->transport->request->getHeaderLine('X-Foo'));
        $this->assertEquals(UriHelper::buildQuery($data), $this->transport->request->getBody()->__toString());
    }

    /**
     * Method to test trace().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::trace
     */
    public function testTrace()
    {
        $url = 'http://example.com/?foo=bar';
        $headers = ['X-Foo' => 'Bar'];

        $this->instance->trace($url, $headers);

        $this->assertEquals('TRACE', $this->transport->request->getMethod());
        $this->assertEquals($url, $this->transport->request->getRequestTarget());
        $this->assertEquals('Bar', $this->transport->request->getHeaderLine('X-Foo'));
    }

    /**
     * Method to test patch().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::patch
     */
    public function testPatch()
    {
        $url = 'http://example.com/?foo=bar';
        $data = ['flower' => 'sakura'];
        $headers = ['X-Foo' => 'Bar'];

        $this->instance->patch($url, $data, $headers);

        $this->assertEquals('PATCH', $this->transport->request->getMethod());
        $this->assertEquals($url, $this->transport->request->getRequestTarget());
        $this->assertEquals('Bar', $this->transport->request->getHeaderLine('X-Foo'));
        $this->assertEquals(UriHelper::buildQuery($data), $this->transport->request->getBody()->__toString());
    }

    /**
     * Method to test getOption().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::getOption
     * @TODO   Implement testGetOption().
     */
    public function testGetOption()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test setOption().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::setOption
     * @TODO   Implement testSetOption().
     */
    public function testSetOption()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test getOptions().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::getOptions
     * @TODO   Implement testGetOptions().
     */
    public function testGetOptions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test setOptions().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::setOptions
     * @TODO   Implement testSetOptions().
     */
    public function testSetOptions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test getTransport().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::getTransport
     * @TODO   Implement testGetTransport().
     */
    public function testGetTransport()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test setTransport().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\HttpClient::setTransport
     * @TODO   Implement testSetTransport().
     */
    public function testSetTransport()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
