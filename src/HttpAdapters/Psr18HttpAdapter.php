<?php

declare(strict_types=1);

namespace MusicBrainz\HttpAdapters;

use MusicBrainz\Exception;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

final class Psr18HttpAdapter extends AbstractHttpAdapter
{
    private readonly int $requestDelaySeconds;

    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly UriFactoryInterface $uriFactory,
        ?string $endpoint = null,
        int $requestDelaySeconds = 1
    ) {
        if ($requestDelaySeconds < 0) {
            throw new \InvalidArgumentException('The request delay cannot be negative.');
        }
        $this->requestDelaySeconds = $requestDelaySeconds;

        if ($endpoint !== null && filter_var($endpoint, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('The endpoint must be a valid URL.');
        }

        if ($endpoint !== null) {
            $this->endpoint = rtrim($endpoint, '/');
        }
    }

    public function call($path, array $params = [], array $options = [], $isAuthRequired = false, $returnArray = false)
    {
        if (($options['user-agent'] ?? '') === '') {
            throw new Exception('You must set a valid User Agent before accessing the MusicBrainz API');
        }

        if ($isAuthRequired && (($options['user'] ?? null) === null || ($options['password'] ?? null) === null)) {
            throw new Exception('Authentication is required');
        }

        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $uri = $this->endpoint . '/' . ltrim((string) $path, '/');
        if ($query !== '') {
            $uri .= '?' . $query;
        }

        $request = $this->requestFactory
            ->createRequest((string) ($options['method'] ?? 'GET'), $this->uriFactory->createUri($uri))
            ->withHeader('Accept', 'application/json')
            ->withHeader('User-Agent', (string) $options['user-agent']);

        if ($isAuthRequired) {
            $request = $request->withHeader(
                'Authorization',
                'Basic ' . base64_encode((string) $options['user'] . ':' . (string) $options['password'])
            );
        }

        if ($this->requestDelaySeconds > 0) {
            sleep($this->requestDelaySeconds);
        }

        $response = $this->client->sendRequest($request);

        return json_decode((string) $response->getBody(), true);
    }
}
