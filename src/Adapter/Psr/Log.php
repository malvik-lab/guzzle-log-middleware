<?php

namespace MalvikLab\GuzzleLogMiddleware\Adapter\Psr;

class Log extends \MalvikLab\GuzzleLogMiddleware\Adapter\AbstractAdapter {
    public function defaultTemplate(): string
    {
        return \GuzzleHttp\MessageFormatter::CLF;
    }

    public function save(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response): void
    {
        $content = $this->prepareContent($request, $response);
        $this->adapter->debug($content);
    }
}