<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Legacy\Http\Test\Stub;

use Psr\Http\Message\StreamInterface;
use Windwalker\Legacy\Http\Response\AbstractContentTypeResponse;
use Windwalker\Legacy\Http\Stream\Stream;

/**
 * The StubContentTypeResponse class.
 *
 * @since  3.0
 */
class StubContentTypeResponse extends AbstractContentTypeResponse
{
    /**
     * Handle body to stream object.
     *
     * @param   string $body The body data.
     *
     * @return  StreamInterface  Converted to stream object.
     */
    protected function handleBody($body)
    {
        $stream = new Stream('php://memory', 'rw+');
        $stream->write($body);
        $stream->rewind();

        return $stream;
    }
}
