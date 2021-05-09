# Guzzle Log Middleware
Log every request and response of your [Guzzle client](https://github.com/guzzle/guzzle). You can set different adapters according to your needs:
  - [PSR-3 Logger Interface](https://www.php-fig.org/psr/psr-3)
  - [PSR-6 Caching Interface](https://www.php-fig.org/psr/psr-6)
  - Filesystem
  
 ## Installation
```
$ composer require malvik-lab/guzzle-log-middleware
```

## Prepare
#### With PSR-3 Logger Interface
###### In this example I have chosen [Laminas Log](https://github.com/laminas/laminas-log)

```php
// composer require laminas/laminas-log laminas/laminas-serializer

$writer = new \Laminas\Log\Writer\Stream('/path/to/log/file.log');
$laminasLogLogger = new \Laminas\Log\Logger();
$laminasLogLogger->addWriter($writer);
$log = new \Laminas\Log\PsrLoggerAdapter($laminasLogLogger);
$adapter = [
    'adapter' => $log,
    'options' => [
        'template' => \GuzzleHttp\MessageFormatter::CLF, // Not mandatory. For more information: https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php
    ]
];
```

## Prepare
#### With PSR-6 Caching Interface
###### In this example I have chosen [Laminas Cache](https://github.com/laminas/laminas-cache) and Redis

```php
// composer require laminas/laminas-cache laminas/laminas-serializer

$storage = \Laminas\Cache\StorageFactory::factory([
    'adapter' => [
        'name' => 'redis',
        'options' => [
            'namespace' => '',
            'namespace_separator' => '_',
            'ttl' => 3600,
            'password' => 'my-redis-password',
            'server' => [
                'host' => 'my-redis-host',
                'port' => 6379,
            ],
        ],
    ],
    'plugins' => [
        'serializer',
    ],
]);
$cache = new \Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator($storage);
$adapter = [
    'adapter' => $cache,
    'options' => [
        'keyPrefix' => 'my-redis-key-prefix', // Not mandatory
        'template' => \GuzzleHttp\MessageFormatter::CLF, // Not mandatory. For more information: https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php
    ]
];
```

## Prepare
#### With Filesystem
###### In this example I have chosen to save each request and response in a single separate file
```php
$adapter = [
    'adapter' => 'filesystem',
    'options' => [
        'dirPath' => '/path/to/log/folder',
        'template' => \GuzzleHttp\MessageFormatter::CLF, // Not mandatory. For more information: https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php
    ]
];
```

## Prepare
#### With Filesystem
###### In this example I have chosen to save all requests and responses in one file
```php
$adapter = [
    'adapter' => 'filesystem',
    'options' => [
        'filePath' => '/path/to/log/file.log',
        'template' => \GuzzleHttp\MessageFormatter::CLF, // Not mandatory. For more information: https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php
    ]
];
```

## Usage
```php
// In the constructor you can inject one or more adapters

$guzzleLogMiddleware = new \MalvikLab\GuzzleLogMiddleware\GuzzleLogMiddleware([
    $adapter
]);

$stack = \GuzzleHttp\HandlerStack::create();
$stack->push($guzzleLogMiddleware);

// your client
$client = new \GuzzleHttp\Client([
    'handler' => $stack
]);
```

## Output Example
```
>>>>>>>>
POST /app/login HTTP/1.1
Content-Length: 74
User-Agent: GuzzleHttp/7
Host: localhost

{username":"me","password":"my-password"}
<<<<<<<<
HTTP/1.1 200 OK
Date: Mon, 21 Dec 2020 13:58:26 GMT
Server: Apache/2.4.46 (Ubuntu)
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate
Pragma: no-cache
Access-Control-Allow-Origin: *
Content-Length: 284
Content-Type: application/json

{"token": "random-token","expireDatetime": "2020-12-21T15:28:26+01:00"}
--------
NULL
```