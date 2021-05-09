<?php

namespace MalvikLab\GuzzleLogMiddleware;

use MalvikLab\GuzzleLogMiddleware\Exception\GuzzleLogMiddlewareException;
use MalvikLab\GuzzleLogMiddleware\Normalize\AdapterAndOptions;
use MalvikLab\GuzzleLogMiddleware\Adapter\Psr\Cache as CacheAdapter;
use MalvikLab\GuzzleLogMiddleware\Adapter\Psr\Log as LogAdapter;
use MalvikLab\GuzzleLogMiddleware\Adapter\FileSystem as FileSystemAdapter;
use MalvikLab\GuzzleLogMiddleware\Util\Util;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\RequestInterface;

class GuzzleLogMiddleware {
    const NAME = 'GUZZLE LOG MIDDLEWARE';
    const VERSION = '1.0.0';

    private $adapters = [];

    function __construct(array $adaptersAndOptions = [])
    {
        if ( count($adaptersAndOptions) < 1 )
        {
            throw new GuzzleLogMiddlewareException(
                Util::exception(__FUNCTION__, 'No one adapter set')
            );
        }

        foreach ( $adaptersAndOptions as $adapterAndOptions )
        {
            $adapterAndOptions = AdapterAndOptions::normalize($adapterAndOptions);

            switch(true)
            {
                case $adapterAndOptions['adapter'] instanceof CacheItemPoolInterface:
                    $this->adapters[] = new CacheAdapter($adapterAndOptions['adapter'], $adapterAndOptions['options']);
                    break;

                case $adapterAndOptions['adapter'] instanceof LoggerInterface:
                    $this->adapters[] = new LogAdapter($adapterAndOptions['adapter'], $adapterAndOptions['options']);
                    break;

                case 'filesystem' === $adapterAndOptions['adapter']:
                    $this->adapters[] = new FileSystemAdapter($adapterAndOptions['adapter'], $adapterAndOptions['options']);
                    break;

                default:
                    throw new GuzzleLogMiddlewareException(
                        Util::exception(__FUNCTION__, sprintf('Invalid adapter "%s"', $adapterAndOptions['adapter']))
                    );
                    break;
            }
        }
    }

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $promise = $handler($request, $options);
            return $promise->then(
                function ($response) use ($request) {
                    foreach ( $this->adapters as $adapter )
                    {
                        $adapter->save($request, $response);
                    }

                    return $response;
                }
            );
        };
    }
}