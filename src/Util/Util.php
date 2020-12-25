<?php

namespace GuzzleLogMiddleware\Util;

class Util {
    public static function exception(string $title, string $message): string
    {
        return sprintf('[ %s EXCEPTION ] [ %s ] %s', \GuzzleLogMiddleware\GuzzleLogMiddleware::NAME, strtoupper(preg_replace('/(?<!\ )[A-Z]/', ' $0', $title)), $message);
    }
}