<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Legacy\Http\Test\Mock;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Windwalker\Legacy\Http\Response\Response;
use Windwalker\Legacy\Http\Transport\AbstractTransport;

/**
 * The MockTransport class.
 *
 * @since  2.1
 */
class MockTransport extends AbstractTransport
{
    /**
     * Property request.
     *
     * @var  RequestInterface
     */
    public $request;

    /**
     * Send a request to the server and return a Response object with the response.
     *
     * @param   RequestInterface $request The request object to send.
     *
     * @return  ResponseInterface
     *
     * @since   2.1
     */
    public function request(RequestInterface $request)
    {
        $this->request = $request;

        return $this->doRequest($request);
    }

    /**
     * Send a request to the server and return a Response object with the response.
     *
     * @param   RequestInterface $request The request object to store request params.
     *
     * @return  ResponseInterface
     *
     * @since   2.1
     */
    protected function doRequest(RequestInterface $request)
    {
        return new Response();
    }

    /**
     * Method to check if HTTP transport layer available for using
     *
     * @return  boolean  True if available else false
     *
     * @since   2.1
     */
    public static function isSupported()
    {
        return true;
    }

    /**
     * Use stream to download file.
     *
     * @param   RequestInterface       $request The request object to store request params.
     * @param   string|StreamInterface $dest    The dest path to store file.
     *
     * @return  ResponseInterface
     * @since   2.1
     */
    public function download(RequestInterface $request, $dest)
    {
        $this->setOption('target_file', $dest);

        return $this->request($request);
    }
}
