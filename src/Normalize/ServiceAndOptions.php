<?php

namespace GuzzleLogMiddleware\Normalize;

class ServiceAndOptions {
    public static function normalize($serviceAndOptions): array
    {
        $return = [];

        if ( is_array($serviceAndOptions) )
        {
            if ( array_key_exists('service', $serviceAndOptions) )
            {
                $return['service'] = $serviceAndOptions['service'];

                if ( array_key_exists('options', $serviceAndOptions) AND is_array($serviceAndOptions['options']) )
                {
                    $return['options'] = $serviceAndOptions['options'];
                } else {
                    $return['options'] = [];
                }
            }
        }

        return $return;
    }
}