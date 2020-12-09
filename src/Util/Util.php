<?php

namespace GuzzleLogMiddleware\Util;

class Util {
    static public function prepareContent(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response, string $template): string
    {
        return (new \GuzzleHttp\MessageFormatter($template))->format($request, $response);
    }
}