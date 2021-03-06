<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\Http\Test;

use Windwalker\Legacy\Http\AbstractMessage;
use Windwalker\Legacy\Http\Stream\Stream;
use Windwalker\Legacy\Http\Test\Stub\StubMessage;
use Windwalker\Legacy\Test\TestCase\AbstractBaseTestCase;

/**
 * Test class of AbstractMessage
 *
 * @since 2.1
 */
class AbstractMessageTest extends AbstractBaseTestCase
{
    /**
     * Test instance.
     *
     * @var AbstractMessage
     */
    protected $message;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->message = new StubMessage();
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
     * Method to test getProtocolVersion().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\AbstractMessage::getProtocolVersion
     * @covers \Windwalker\Legacy\Http\AbstractMessage::withProtocolVersion
     */
    public function testWithAndSetProtocolVersion()
    {
        $this->assertEquals('1.1', $this->message->getProtocolVersion());

        $message = $this->message->withProtocolVersion('1.0');

        $this->assertNotSame($this->message, $message);
        $this->assertEquals('1.0', $message->getProtocolVersion());

        // Wrong type
        $this->assertExpectedException(
            function () use ($message) {
                $message->withProtocolVersion(1.0);
            },
            'InvalidArgumentException'
        );
    }

    /**
     * Method to test getHeader().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\AbstractMessage::getHeader
     * @covers \Windwalker\Legacy\Http\AbstractMessage::withHeader
     */
    public function testWithAndGetHeader()
    {
        $message = $this->message->withHeader('Content-Type', 'text/json');

        $this->assertNotSame($this->message, $message);
        $this->assertEquals(['text/json'], $message->getHeader('Content-Type'));
        $this->assertEquals(['text/json'], $message->getHeader('content-type'));

        $message = $this->message->withHeader('X-Foo', ['Foo', 'Bar']);

        $this->assertNotSame($this->message, $message);
        $this->assertEquals(['Foo', 'Bar'], $message->getHeader('X-Foo'));
    }

    /**
     * Method to test hasHeader().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\AbstractMessage::hasHeader
     */
    public function testHasHeader()
    {
        $this->assertFalse($this->message->hasHeader('X-Foo'));

        $message = $this->message->withHeader('Content-Type', 'text/json');

        $this->assertTrue($message->hasHeader('Content-Type'));
        $this->assertTrue($message->hasHeader('content-type'));
    }

    /**
     * Method to test getHeaders().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\AbstractMessage::getHeaders
     */
    public function testGetHeaders()
    {
        $this->assertEquals([], $this->message->getHeaders());

        $message = $this->message->withHeader('X-Foo', ['Foo', 'Bar']);
        $message = $message->withHeader('X-Bar', ['Flower', 'Sakura']);

        $expected = [
            'X-Foo' => ['Foo', 'Bar'],
            'X-Bar' => ['Flower', 'Sakura'],
        ];

        $this->assertEquals($expected, $message->getHeaders());
    }

    /**
     * Method to test getHeaderLine().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\AbstractMessage::getHeaderLine
     */
    public function testGetHeaderLine()
    {
        $this->assertEquals('', $this->message->getHeaderLine('X-Foo'));

        $message = $this->message->withHeader('X-Foo', ['Foo', 'Bar']);

        $this->assertEquals('Foo,Bar', $message->getHeaderLine('X-Foo'));
        $this->assertEquals('Foo,Bar', $message->getHeaderLine('x-foo'));
        $this->assertSame('', $message->getHeaderLine('x-bar'));
    }

    /**
     * Method to test withAddedHeader().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\AbstractMessage::withAddedHeader
     */
    public function testWithAddedHeader()
    {
        $message = $this->message->withAddedHeader('X-Foo', 'One');

        $this->assertNotSame($this->message, $message);
        $this->assertEquals(['One'], $message->getHeader('X-Foo'));

        $message = $message->withAddedHeader('X-Foo', 'Two');

        $this->assertEquals(['One', 'Two'], $message->getHeader('X-Foo'));

        $message = $message->withAddedHeader('X-Foo', ['Three', 'Four']);

        $this->assertEquals(['One', 'Two', 'Three', 'Four'], $message->getHeader('X-Foo'));
    }

    /**
     * Method to test withoutHeader().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\AbstractMessage::withoutHeader
     */
    public function testWithoutHeader()
    {
        $message = $this->message->withAddedHeader('X-Foo', 'One');

        $this->assertNotSame($this->message, $message);
        $this->assertEquals(['One'], $message->getHeader('X-Foo'));

        $message2 = $message->withoutHeader('X-Foo');

        $this->assertNotSame($this->message, $message2);
        $this->assertEquals([], $message2->getHeader('X-Foo'));

        $message3 = $message->withoutHeader('x-foo');

        $this->assertNotSame($this->message, $message3);
        $this->assertEquals([], $message3->getHeader('X-Foo'));
    }

    /**
     * Method to test getBody().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\AbstractMessage::getBody
     */
    public function testWithAndGetBody()
    {
        $message = $this->message->withBody(new Stream());

        $this->assertNotSame($this->message, $message);
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $message->getBody());
    }
}
