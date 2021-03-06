<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Legacy\Http\Response;

use Psr\Http\Message\StreamInterface;
use Windwalker\Legacy\Http\Stream\Stream;

/**
 * The TextResponse class.
 *
 * @since  3.0
 */
class TextResponse extends AbstractContentTypeResponse
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
        if (is_string($body)) {
            $stream = new Stream('php://temp', 'wb+');
            $stream->write($body);
            $stream->rewind();

            $body = $stream;
        }

        if (!$body instanceof StreamInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid body content type %s, please provide string or StreamInterface',
                    gettype($body)
                )
            );
        }

        return $body;
    }
}
