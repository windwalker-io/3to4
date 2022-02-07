<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\Http\Test\Helper;

use Windwalker\Legacy\Http\Helper\StreamHelper;
use Windwalker\Legacy\Http\Response\Response;
use Windwalker\Legacy\Http\Stream\Stream;
use Windwalker\Legacy\Http\Test\Stub\StubStreamOutput;

/**
 * Test class of StreamHelper
 *
 * @since 3.0
 */
class StreamHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Method to test copy().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Helper\StreamHelper::copy
     */
    public function testCopy()
    {
        $src = new Stream(__FILE__, Stream::MODE_READ_ONLY_FROM_BEGIN);
        $dest = new Stream('php://memory', Stream::MODE_READ_WRITE_FROM_BEGIN);

        StreamHelper::copy($src, $dest);

        $this->assertEquals($src->__toString(), $dest->__toString());
    }

    /**
     * Method to test copyTo().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Helper\StreamHelper::copyTo
     */
    public function testCopyTo()
    {
        $dest = __DIR__ . '/test.txt';

        StreamHelper::copyTo($src = new Stream(__FILE__, Stream::MODE_READ_ONLY_FROM_BEGIN), $dest);

        $this->assertEquals($src->__toString(), file_get_contents($dest));

        @unlink($dest);
    }

    /**
     * Method to test copyFrom().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Helper\StreamHelper::copyFrom
     */
    public function testCopyFrom()
    {
        StreamHelper::copyFrom(__FILE__, $dest = new Stream('php://memory', Stream::MODE_READ_WRITE_FROM_BEGIN));

        $this->assertEquals(file_get_contents(__FILE__), $dest->__toString());
    }

    /**
     * Method to test sendAttachment().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\Http\Helper\StreamHelper::sendAttachment
     */
    public function testSendAttachment()
    {
        StreamHelper::$outputObject = new StubStreamOutput();

        StreamHelper::sendAttachment(__FILE__, $response = new Response());

        $this->assertEquals(file_get_contents(__FILE__), StreamHelper::$outputObject->output);
        $this->assertEquals(
            ['application/octet-stream'],
            StreamHelper::$outputObject->message->getHeader('content-type')
        );
    }
}
