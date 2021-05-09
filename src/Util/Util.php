<?php

namespace MalvikLab\GuzzleLogMiddleware\Util;

use MalvikLab\GuzzleLogMiddleware\GuzzleLogMiddleware;

class Util {
    public static function exception(string $title, string $message): string
    {
        return sprintf('[ %s EXCEPTION ] [ %s ] %s', GuzzleLogMiddleware::NAME, strtoupper(preg_replace('/(?<!\ )[A-Z]/', ' $0', $title)), $message);
    }
}