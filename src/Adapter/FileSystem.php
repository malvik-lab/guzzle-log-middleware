<?php

namespace GuzzleLogMiddleware\Adapter;

class FileSystem extends \GuzzleLogMiddleware\Adapter\AbstractAdapter {
    function __construct($adapter, array $options = [])
    {
        parent::__construct($adapter, $options);

        if ( is_null($this->options['dirPath']) AND is_null($this->options['filePath']) )
        {
            throw new Exception\FileSystem(
                \GuzzleLogMiddleware\Util\Util::exception(__FUNCTION__, 'Set at least the "dirPath" or "filePath"')
            );
        }

        if ( !is_null($this->options['dirPath']) )
        {
            $info = pathinfo($this->options['dirPath']);
            $this->options['dirPath'] = sprintf('%s/%s', $info['dirname'], $info['filename']);

            if ( !is_writable($this->options['dirPath']) )
            {
                throw new Exception\FileSystem(
                    \GuzzleLogMiddleware\Util\Util::exception(__FUNCTION__, sprintf('Destination folder "%s" is not writable', $this->options['dirPath']))
                );
            }
        }
    }

    public function defaultTemplate(): string
    {
        return \GuzzleHttp\MessageFormatter::DEBUG;
    }

    public function save(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response): void
    {
        $content = $this->prepareContent($request, $response);

        switch(true)
        {
            case !is_null($this->options['dirPath']):
                $now = new \DateTimeImmutable();

                $fileName = sprintf('%s%s', $now->format('Y-m-d H.i.s-u'), '.log');
                $filePath = sprintf('%s/%s', $this->options['dirPath'], $fileName);
        
                file_put_contents($filePath, $content);
                break;

            case !is_null($this->options['filePath']):
                $content .= "\n\n\n\n\n\n============================================================================================================\n\n\n\n\n\n";
                file_put_contents($this->options['filePath'], $content, FILE_APPEND);
                break;
        }
    }
}