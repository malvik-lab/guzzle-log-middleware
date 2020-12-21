<?php

namespace GuzzleLogMiddleware\Util;

class Util {
    static public function prepareContent(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response, string $template): string
    {
        return (new \GuzzleHttp\MessageFormatter($template))->format($request, $response);
    }

    public static function exception(string $title, string $message): string
    {
        return sprintf('[ %s EXCEPTION ] [ %s ] %s', \GuzzleLogMiddleware\GuzzleLogMiddleware::NAME, strtoupper(preg_replace('/(?<!\ )[A-Z]/', ' $0', $title)), $message);
    }
}