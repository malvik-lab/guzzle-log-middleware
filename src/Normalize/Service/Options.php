<?php

namespace GuzzleLogMiddleware\Normalize\Service;

class Options {
    public static function normalize(array $options): array
    {
        $return = [];

        if ( array_key_exists('template', $options) AND is_string($options['template']) )
        {
            $return['template'] = $options['template'];
        } else {
            $return['template'] = null;
        }

        if ( array_key_exists('keyPrefix', $options) AND is_string($options['keyPrefix']) )
        {
            $return['keyPrefix'] = $options['keyPrefix'];
        } else {
            $return['keyPrefix'] = null;
        }

        return $return;
    }
}