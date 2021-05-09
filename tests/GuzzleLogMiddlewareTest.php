<?php declare(strict_types=1);

/**
 * REDIS_HOST=localhost REDIS_PORT=6379 vendor/bin/phpunit tests
 */

final class GuzzleLogMiddlewareTest extends PHPUnit\Framework\TestCase {
    protected static $filesystem;
    protected static $dataPath;
    protected static $filesystemPath;

    public static function setUpBeforeClass(): void
    {
        self::$filesystem = new \Symfony\Component\Filesystem\Filesystem();
        self::$dataPath = sprintf('%s/data', dirname(__FILE__));
        self::$filesystemPath = sprintf('%s/filesystem', self::$dataPath);

        if ( !self::$filesystem->exists(self::$dataPath) )
        {
            self::$filesystem->mkdir(self::$dataPath, 0755);
        }

        if ( !self::$filesystem->exists(self::$filesystemPath) )
        {
            self::$filesystem->mkdir(self::$filesystemPath, 0755);
        }
    }

    public static function tearDownAfterClass(): void
    {
        self::$filesystem->remove(self::$dataPath);
    }

    /**
     * vendor/bin/phpunit --filter testNoAdapters tests
     */
    public function testNoAdapters(): void
    {
        $this->expectException(\MalvikLab\GuzzleLogMiddleware\Exception\GuzzleLogMiddlewareException::class);
        $this->exec([]);
    }

    /**
     * vendor/bin/phpunit --filter testLog tests
     */
    public function testLog(): void
    {
        $adapters = [];

        $loggerFilePath = sprintf('%s/data/logger.log', dirname(__FILE__));
        $writer = new \Laminas\Log\Writer\Stream($loggerFilePath);
        $laminasLogLogger = new \Laminas\Log\Logger();
        $laminasLogLogger->addWriter($writer);
        $log = new \Laminas\Log\PsrLoggerAdapter($laminasLogLogger);
        $adapters[] = [
            'adapter' => $log,
            'options' => [
                'template' => \GuzzleHttp\MessageFormatter::CLF
            ]
        ];

        $this->exec($adapters);
    }

    /**
     * REDIS_HOST=localhost REDIS_PORT=6379 vendor/bin/phpunit --filter testCache tests
     */
    public function testCache()
    {
        $adapters = [];

        $storage = \Laminas\Cache\StorageFactory::factory([
            'adapter' => [
                'name' => 'redis',
                'options' => [
                    'namespace' => '',
                    'namespace_separator' => '_',
                    'ttl' => 3600,
                    'password' => getenv('REDIS_PASSWORD'),
                    'server' => [
                        'host' => getenv('REDIS_HOST'),
                        'port' => getenv('REDIS_PORT'),
                    ],
                ],
            ],
            'plugins' => [
                'serializer',
            ],
        ]);
        $cache = new \Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator($storage);
        $adapters[] = [
            'adapter' => $cache,
            'options' => [
                'keyPrefix' => 'TEST-',
                'template' => \GuzzleHttp\MessageFormatter::CLF,
            ],
        ];

        $this->exec($adapters);
    }

    /**
     * vendor/bin/phpunit --filter testFilesystem tests
     */
    public function testFilesystem()
    {
        $adapters = [
            [
                'adapter' => 'filesystem',
                'options' => [
                    'filePath' => sprintf('%s/data/filesystem.log', dirname(__FILE__)),
                    'template' => \GuzzleHttp\MessageFormatter::CLF,
                ],
            ],
            [
                'adapter' => 'filesystem',
                'options' => [
                    'dirPath' => sprintf('%s/data/filesystem', dirname(__FILE__)),
                    'template' => \GuzzleHttp\MessageFormatter::CLF,
                ],
            ],
        ];

        $this->exec($adapters);
    }

    protected function exec(array $adapters = [])
    {
        $guzzleLogMiddleware = new \MalvikLab\GuzzleLogMiddleware\GuzzleLogMiddleware($adapters);

        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->push($guzzleLogMiddleware);

        $client = new \GuzzleHttp\Client(['handler' => $stack]);
        $res = $client->get('https://jsonplaceholder.typicode.com/users');

        $this->assertIsInt($res->getStatusCode());
        $this->assertEquals($res->getStatusCode(), 200);
    }
}
