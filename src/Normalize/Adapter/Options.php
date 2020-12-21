<?php

namespace GuzzleLogMiddleware\Normalize\Adapter;

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

        if ( array_key_exists('dirPath', $options) AND is_string($options['dirPath']) )
        {
            $return['dirPath'] = $options['dirPath'];
        } else {
            $return['dirPath'] = null;
        }

        if ( array_key_exists('filePath', $options) AND is_string($options['filePath']) )
        {
            $return['filePath'] = $options['filePath'];
        } else {
            $return['filePath'] = null;
        }

        return $return;
    }
}