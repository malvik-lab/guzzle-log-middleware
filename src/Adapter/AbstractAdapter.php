<?php

namespace MalvikLab\GuzzleLogMiddleware\Adapter;

use MalvikLab\GuzzleLogMiddleware\Normalize\Adapter\Options;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\MessageFormatter;

abstract class AbstractAdapter implements AdapterInterface {
    public $adapter;
    public $options;

    function __construct($adapter, array $options = [])
    {
        $this->adapter = $adapter;
        $this->options = Options::normalize($options);

        if ( is_null($this->options['template']) )
        {
            $this->options['template'] = $this->defaultTemplate();
        }
    }

    public function prepareContent(RequestInterface $request, ResponseInterface $response): string
    {
        return (new MessageFormatter($this->options['template']))->format($request, $response);
    }
}