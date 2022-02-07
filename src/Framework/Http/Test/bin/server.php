<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later.
 */

// phpcs:disable

use Psr\Http\Message\ResponseInterface;

include_once __DIR__ . '/../../../../vendor/autoload.php';

//show($request = \Windwalker\Legacy\Http\ServerRequestFactory::fromGlobals());
//
//show($request->getUri());

$server = \Windwalker\Legacy\Http\WebHttpServer::create(
    function ($request, ResponseInterface $response, $finalHandler) {
        // $response = $response->withHeader('Content-Type', 'application/json');

//    $response->getBody()->write('Hello World!');

        $response = new \Windwalker\Legacy\Http\Response\HtmlResponse('<root><f>中文 World!</f></root>');

        $response = $response->withHeader('asd', 123);

        $response = $finalHandler($request, $response);

        return $response;
    },
    \Windwalker\Legacy\Http\Request\ServerRequestFactory::createFromGlobals(),
    new \Windwalker\Legacy\Http\Response\HtmlResponse()
);

$server->listen(
    function ($request, ResponseInterface $response) use ($server) {
        $server->cachable($server::CACHE_CUSTOM_HEADER);

        return $response = $server->getCompressor()->compress($response);
    }
);
