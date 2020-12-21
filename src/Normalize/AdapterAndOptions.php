<?php

namespace GuzzleLogMiddleware\Normalize;

class AdapterAndOptions {
    public static function normalize($serviceAndOptions): array
    {
        $return = [];

        if ( is_array($serviceAndOptions) )
        {
            if ( array_key_exists('adapter', $serviceAndOptions) )
            {
                $return['adapter'] = $serviceAndOptions['adapter'];

                if ( array_key_exists('options', $serviceAndOptions) AND is_array($serviceAndOptions['options']) )
                {
                    $return['options'] = $serviceAndOptions['options'];
                } else {
                    $return['options'] = [];
                }
            } else {
                $return['adapter'] = null;
            }
        }

        return $return;
    }
}