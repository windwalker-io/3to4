<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later.
 */

use Psr\Http\Message\ResponseInterface;

include_once __DIR__ . '/../../../../vendor/autoload.php';

//show($request = \Windwalker\Legacy\Http\ServerRequestFactory::fromGlobals());
//
//show($request->getUri());

$server = \Windwalker\Legacy\Http\WebHttpServer::create(
    function ($request, ResponseInterface $response, $finalHandler) {
        $res = new \Windwalker\Legacy\Http\Response\AttachmentResponse();
        $res = $res->withFile(__DIR__ . '/.htaccess');
        $res = $res->withFilename('Hello.txt');

        return $res;
    },
    \Windwalker\Legacy\Http\Request\ServerRequestFactory::createFromGlobals()
);

$server->listen(
    function ($request, $response) use ($server) {
        return $response;
    }
);
