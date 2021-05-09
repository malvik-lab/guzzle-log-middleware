<?php

namespace MalvikLab\GuzzleLogMiddleware\Adapter\Psr;

class Cache extends \MalvikLab\GuzzleLogMiddleware\Adapter\AbstractAdapter {
    public function defaultTemplate(): string
    {
        return \GuzzleHttp\MessageFormatter::DEBUG;
    }

    public function save(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response): void
    {
        $content = $this->prepareContent($request, $response);

        $now = new \DateTimeImmutable();

        $key = $now->format('Y-m-d H.i.s-u');

        if ( !is_null($this->options['keyPrefix']) )
        {
            $key = sprintf('%s%s', $this->options['keyPrefix'], $key);
        }

        $item = $this->adapter->getItem($key);
        $item->set($content);
        $this->adapter->save($item);
    }
}