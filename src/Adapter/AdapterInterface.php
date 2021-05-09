<?php

namespace MalvikLab\GuzzleLogMiddleware\Adapter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface AdapterInterface {
    public function defaultTemplate(): string;

    public function save(RequestInterface $request, ResponseInterface $response): void;
}