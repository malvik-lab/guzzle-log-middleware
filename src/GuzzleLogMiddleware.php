<?php

namespace GuzzleLogMiddleware;

class GuzzleLogMiddleware {
    private $services = [];

    function __construct(array $servicesAndOptions = [])
    {
        foreach ( $servicesAndOptions as $serviceAndOptions )
        {
            $serviceAndOptions = Normalize\ServiceAndOptions::normalize($serviceAndOptions);

            switch(true)
            {
                case $serviceAndOptions['service'] instanceof \Psr\Cache\CacheItemPoolInterface:
                    $this->services[] = new Adapter\Psr\Cache($serviceAndOptions['service'], $serviceAndOptions['options']);
                    break;

                case $serviceAndOptions['service'] instanceof \Psr\Log\LoggerInterface:
                    $this->services[] = new Adapter\Psr\Log($serviceAndOptions['service'], $serviceAndOptions['options']);
                    break;

                default:
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
                    foreach ( $this->services as $service )
                    {
                        $service->save($request, $response);
                    }

                    return $response;
                }
            );
        };
    }
}