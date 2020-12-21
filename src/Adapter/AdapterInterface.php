<?php

namespace GuzzleLogMiddleware\Adapter;

interface AdapterInterface {
    public function defaultTemplate(): string;

    public function save(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response): void;
}