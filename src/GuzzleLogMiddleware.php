<?php

namespace GuzzleLogMiddleware;

class GuzzleLogMiddleware {
    const NAME = 'GUZZLE LOG MIDDLEWARE';
    const VERSION = '0.0.3';

    private $adapters = [];

    function __construct(array $adaptersAndOptions = [])
    {
        if ( count($adaptersAndOptions) < 1 )
        {
            throw new Exception\GuzzleLogMiddleware(
                Util\Util::exception(__FUNCTION__, 'No one adapter set')
            );
        }

        foreach ( $adaptersAndOptions as $adapterAndOptions )
        {
            $adapterAndOptions = Normalize\AdapterAndOptions::normalize($adapterAndOptions);

            switch(true)
            {
                case $adapterAndOptions['adapter'] instanceof \Psr\Cache\CacheItemPoolInterface:
                    $this->adapters[] = new Adapter\Psr\Cache($adapterAndOptions['adapter'], $adapterAndOptions['options']);
                    break;

                case $adapterAndOptions['adapter'] instanceof \Psr\Log\LoggerInterface:
                    $this->adapters[] = new Adapter\Psr\Log($adapterAndOptions['adapter'], $adapterAndOptions['options']);
                    break;

                case 'filesystem' === $adapterAndOptions['adapter']:
                    $this->adapters[] = new Adapter\FileSystem($adapterAndOptions['adapter'], $adapterAndOptions['options']);
                    break;

                default:
                    throw new Exception\GuzzleLogMiddleware(
                        Util\Util::exception(__FUNCTION__, sprintf('Invalid adapter "%s"', $adapterAndOptions['adapter']))
                    );
                    break;
            }
        }
    }

    public function __invoke(callable $handler)
    {
        return function (\Psr\Http\Message\RequestInterface $request, array $options) use ($handler) {
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