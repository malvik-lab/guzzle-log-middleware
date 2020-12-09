<?php

namespace GuzzleLogMiddleware\Adapter;

abstract class AbstractAdapter implements AdapterInterface {
    public $service;
    public $options;

    function __construct($service, array $options = [])
    {
        $this->service = $service;
        $this->options = \GuzzleLogMiddleware\Normalize\Service\Options::normalize($options);

        if ( is_null($this->options['template']) )
        {
            $this->options['template'] = $this->defaultTemplate();
        }
    }

    public function prepareContent(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response): string
    {
        return (new \GuzzleHttp\MessageFormatter($this->options['template']))->format($request, $response);
    }
}