<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\Http\Test\Output;

use Windwalker\Legacy\Http\Output\HttpCompressor;
use Windwalker\Legacy\Http\Response\TextResponse;
use Windwalker\Legacy\Http\Test\Stub\StubHttpCompressor;

/**
 * Test class of HttpCompressor
 *
 * @since 3.0
 */
class HttpCompressorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test instance.
     *
     * @var HttpCompressor
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
        if (!HttpCompressor::isSupported()) {
            $this->markTestSkipped('This environment not support zlib');
        }

        $this->instance = new StubHttpCompressor('gzip, deflate');
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
     * Method to test isSupported().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Output\HttpCompressor::isSupported
     */
    public function testIsSupported()
    {
        $this->assertEquals(
            extension_loaded('zlib') || ini_get('zlib.output_compression'),
            HttpCompressor::isSupported()
        );
    }

    /**
     * Method to test compress().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Output\HttpCompressor::compress
     */
    public function testCompress()
    {
        // Gzip
        $this->instance->setAcceptEncoding('gzip');

        $response = $this->instance->compress(new TextResponse('Hello World'));

        $this->assertEquals(gzencode('Hello World', 4, FORCE_GZIP), $response->getBody()->__toString());

        // Deflate
        $this->instance->setAcceptEncoding('deflate');

        $response = $this->instance->compress(new TextResponse('Hello World'));

        $this->assertEquals(gzencode('Hello World', 4, FORCE_DEFLATE), $response->getBody()->__toString());
    }

    /**
     * testCompressData
     *
     * @return  void
     *
     * @covers \Windwalker\Legacy\Http\Output\HttpCompressor::encode
     */
    public function testEncode()
    {
        $this->assertEquals(gzencode('Hello World', 4, FORCE_GZIP), $this->instance->encode('Hello World'));
    }

    /**
     * Method to test getAcceptEncoding().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Output\HttpCompressor::getAcceptEncoding
     * @covers \Windwalker\Legacy\Http\Output\HttpCompressor::setAcceptEncoding
     */
    public function testGetAndSetAcceptEncoding()
    {
        $this->assertEquals('gzip, deflate', $this->instance->getAcceptEncoding());

        $this->instance->setAcceptEncoding('deflate');

        $this->assertEquals('deflate', $this->instance->getAcceptEncoding());
    }

    /**
     * Method to test checkHeadersSent().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Output\HttpCompressor::checkHeadersSent
     */
    public function testCheckHeadersSent()
    {
        $this->markTestSkipped(
            'This method dependent on php function header_sent().'
        );
    }

    /**
     * Method to test checkConnectionAlive().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Output\HttpCompressor::checkConnectionAlive
     */
    public function testCheckConnectionAlive()
    {
        $this->markTestSkipped(
            'This method dependent on php function connection_alive().'
        );
    }

    /**
     * Method to test getEncodedBy().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Output\HttpCompressor::getEncodedBy
     */
    public function testGetAndSetEncodedBy()
    {
        $this->assertEquals('Windwalker', $this->instance->getEncodedBy());

        $this->instance->setEncodedBy('Flower');

        $this->assertEquals('Flower', $this->instance->getEncodedBy());
    }
}
