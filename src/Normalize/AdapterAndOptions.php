<?php

namespace MalvikLab\GuzzleLogMiddleware\Normalize;

class AdapterAndOptions {
    public static function normalize($adapterAndOptions): array
    {
        $return = [];

        if ( is_array($adapterAndOptions) )
        {
            if ( array_key_exists('adapter', $adapterAndOptions) )
            {
                $return['adapter'] = $adapterAndOptions['adapter'];

                if ( array_key_exists('options', $adapterAndOptions) AND is_array($adapterAndOptions['options']) )
                {
                    $return['options'] = $adapterAndOptions['options'];
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