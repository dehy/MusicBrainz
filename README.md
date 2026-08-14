# MusicBrainz Web Service (v2) PHP class

This PHP library that allows you to easily access the MusicBrainz Web Service V2 API. Visit the [MusicBrainz development page](http://musicbrainz.org/doc/Development) for more information.

This project is a fork of https://github.com/mikealmond/MusicBrainz

## Requirements

- PHP 8.3 or later
- A PSR-18 HTTP client and PSR-17 request and URI factories. Guzzle 7 supplies all three.

## Installation

```sh
composer require dehy/musicbrainz guzzlehttp/guzzle
```

Guzzle is used below as one PSR-18 and PSR-17 implementation. Another compatible option is `symfony/http-client`, whose `Symfony\Component\HttpClient\Psr18Client` provides both.

## Usage

```php
<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use MusicBrainz\Filters\RecordingFilter;
use MusicBrainz\HttpAdapters\Psr18HttpAdapter;
use MusicBrainz\MusicBrainz;

require __DIR__ . '/vendor/autoload.php';

$factory = new HttpFactory();
$brainz = new MusicBrainz(new Psr18HttpAdapter(new Client(), $factory, $factory));
$brainz->setUserAgent('ApplicationName', '0.2.0', 'https://example.com');

$args = [
    'recording' => 'Buddy Holly',
    'artist' => 'Weezer',
    'creditname' => 'Weezer',
    'status' => 'Official',
];

try {
    $recordings = $brainz->search(new RecordingFilter($args));
    print_r($recordings);
} catch (\Throwable $exception) {
    print $exception->getMessage();
}
```

More runnable examples are available in the [examples](examples) directory. To try the browser demo locally, run `php -S 127.0.0.1:8080 -t demo` and open `http://127.0.0.1:8080`.

## Development

```sh
composer test
composer stan
composer lint
composer rector
```


## License

**Short:** Use it in any project, no matter if it is commercial or not. Just don't remove the copyright notice.

**MIT License**

Copyright © 2020 Arnaud de Mouhy
Copyright © 2015 Mike Almond

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
