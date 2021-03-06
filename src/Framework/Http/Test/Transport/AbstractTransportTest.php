<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\Http\Test\Transport;

use Psr\Http\Message\StreamInterface;
use Windwalker\Legacy\Http\Request\Request;
use Windwalker\Legacy\Http\Stream\Stream;
use Windwalker\Legacy\Http\Stream\StringStream;
use Windwalker\Legacy\Http\Transport\AbstractTransport;
use Windwalker\Legacy\Uri\PsrUri;
use Windwalker\Legacy\Uri\UriHelper;

/**
 * Test class of CurlTransport
 *
 * @since 2.1
 */
abstract class AbstractTransportTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Property options.
     *
     * @var  array
     */
    protected $options = [
        'options' => [],
    ];

    /**
     * Test instance.
     *
     * @var AbstractTransport
     */
    protected $instance;

    /**
     * Property downloadFile.
     *
     * @var  string
     */
    protected $destFile;

    /**
     * setUpBeforeClass
     *
     * @return  void
     */
    public static function setUpBeforeClass(): void
    {
        if (!defined('WINDWALKER_TEST_HTTP_URL')) {
            static::markTestSkipped('No WINDWALKER_TEST_HTTP_URL provided');
        }
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        if (!$this->instance->isSupported()) {
            $this->markTestSkipped(get_class($this->instance) . ' driver not supported.');
        }

        $this->destFile = __DIR__ . '/downloaded.tmp';
    }

    /**
     * createRequest
     *
     * @param StreamInterface $stream
     *
     * @return Request
     */
    protected function createRequest($stream = null)
    {
        return new Request($stream ?: new StringStream());
    }

    /**
     * testRequestGet
     *
     * @return  void
     */
    public function testRequestGet()
    {
        $request = $this->createRequest();

        $request = $request->withUri(new PsrUri(WINDWALKER_TEST_HTTP_URL))
            ->withMethod('GET');

        $response = $this->instance->request($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->getContents());

        $request = $this->createRequest();

        $request = $request->withUri(new PsrUri(WINDWALKER_TEST_HTTP_URL . '?foo=bar&baz[3]=yoo'))
            ->withMethod('GET');

        $response = $this->instance->request($request);

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(['foo' => 'bar', 'baz' => [3 => 'yoo']], $data['_GET']);
    }

    /**
     * testBadDomainGet
     *
     * @return  void
     */
    public function testBadDomainGet()
    {
        $this->expectException(\RuntimeException::class);

        $request = $this->createRequest();

        $request = $request->withUri(new PsrUri('http://not.exists.url/flower.sakura'))
            ->withMethod('GET');

        $this->instance->request($request);
    }

    /**
     * testBadPathGet
     *
     * @return  void
     */
    public function testBadPathGet()
    {
        $request = $this->createRequest();

        $request = $request->withUri(new PsrUri(dirname(WINDWALKER_TEST_HTTP_URL) . '/wrong.php'))
            ->withMethod('POST');

        $request->getBody()->write(UriHelper::buildQuery(['foo' => 'bar']));

        $response = $this->instance->request($request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getReasonPhrase());
    }

    /**
     * testRequestPost
     *
     * @return  void
     */
    public function testRequestPost()
    {
        $request = $this->createRequest();

        $request = $request->withUri(new PsrUri(WINDWALKER_TEST_HTTP_URL))
            ->withMethod('POST');

        $request->getBody()->write(UriHelper::buildQuery(['foo' => 'bar']));

        $response = $this->instance->request($request);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(['foo' => 'bar'], $data['_POST']);
    }

    /**
     * testRequestPut
     *
     * @return  void
     */
    public function testRequestPut()
    {
        $request = $this->createRequest();

        $request = $request->withUri(new PsrUri(WINDWALKER_TEST_HTTP_URL))
            ->withMethod('PUT');

        $request->getBody()->write(UriHelper::buildQuery(['foo' => 'bar']));

        $response = $this->instance->request($request);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(['foo' => 'bar'], $data['data']);
        $this->assertEquals('PUT', $data['_SERVER']['REQUEST_METHOD']);
    }

    /**
     * testRequestCredentials
     *
     * @return  void
     */
    public function testRequestCredentials()
    {
        $request = $this->createRequest();

        $uri = new PsrUri(WINDWALKER_TEST_HTTP_URL);
        $uri = $uri->withUserInfo('username', 'pass1234');

        $request = $request->withUri($uri)
            ->withMethod('GET');

        $response = $this->instance->request($request);

        $data = json_decode($response->getBody()->getContents(), true);

        if ($data['_SERVER']['GATEWAY_INTERFACE'] ?? null) {
            if (str_contains($data['_SERVER']['GATEWAY_INTERFACE'], 'CGI')) {
                self::markTestSkipped('Target server is CGI, unable to get PHP_AUTH_USER');
            }
        }

        $this->assertEquals('username', $data['_SERVER']['PHP_AUTH_USER']);
        $this->assertEquals('pass1234', $data['_SERVER']['PHP_AUTH_PW']);
    }

    /**
     * testRequestPostScalar
     *
     * @return  void
     */
    public function testRequestPostScalar()
    {
        $request = $this->createRequest();

        $request = $request->withUri(new PsrUri(WINDWALKER_TEST_HTTP_URL . '?foo=bar'))
            ->withMethod('POST');

        $request->getBody()->write('flower=sakura');

        $response = $this->instance->request($request);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(['foo' => 'bar'], $data['_GET']);
        $this->assertEquals(['flower' => 'sakura'], $data['_POST']);
    }

    /**
     * testDownload
     *
     * @return  void
     */
    public function testDownload()
    {
        $this->unlinkDownloaded();

        $this->assertFileDoesNotExist((string) $this->destFile);

        $request = $this->createRequest(new Stream());

        $src = dirname(WINDWALKER_TEST_HTTP_URL) . '/download_stub.txt';

        $request = $request->withUri(new PsrUri($src))
            ->withMethod('GET');

        $response = $this->instance->download($request, $this->destFile);

        $this->assertEquals('This is test download file.', trim(file_get_contents($this->destFile)));
    }

    /**
     * unlinkDownloaded
     *
     * @return  void
     */
    protected function unlinkDownloaded()
    {
        if (is_file($this->destFile)) {
            @unlink($this->destFile);
        }
    }
}
