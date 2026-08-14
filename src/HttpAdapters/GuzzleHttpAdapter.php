<?php

namespace MusicBrainz\HttpAdapters;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use MusicBrainz\Exception;

/**
 * Guzzle Http Adapter
 */
class GuzzleHttpAdapter extends AbstractHttpAdapter
{
    /**
     * Initializes the class.
     *
     * @param \GuzzleHttp\ClientInterface $client The Guzzle client used to make requests
    * @param string|null                   $endpoint Override the default endpoint (useful for local development)
     */
    public function __construct(/**
     * The Guzzle client used to make cURL requests
     */
        private readonly ClientInterface $client,
        ?string $endpoint = null
    ) {
        if ($endpoint !== null && filter_var($endpoint, FILTER_VALIDATE_URL) !== false) {
            $this->endpoint = $endpoint;
        }
    }

    /**
     * Perform an HTTP request on MusicBrainz
     *
     * @param  string  $path
     * @param  array   $params
     * @param  array   $options
     * @param  boolean $isAuthRequired
     * @param  boolean $returnArray disregarded
     *
     * @throws \MusicBrainz\Exception
     * @return array
     */
    public function call($path, array $params = [], array $options = [], $isAuthRequired = false, $returnArray = false)
    {
        if ($options['user-agent'] == '') {
            throw new Exception('You must set a valid User Agent before accessing the MusicBrainz API');
        }

        // We build the query ourselves because Guzzle url-encodes it
        $queryString = urldecode(http_build_query($params, '', '&', PHP_QUERY_RFC1738));

        $requestOptions = [
            "headers" => [
                'Accept' => 'application/json',
                'User-Agent' => $options['user-agent']
            ],
            "query" => $queryString
        ];

        if ($isAuthRequired) {
            if ($options['user'] != null && $options['password'] != null) {
                $requestOptions["auth"] = [$options['user'], $options['password']];
            } else {
                throw new Exception('Authentication is required');
            }
        }

        // musicbrainz throttle
        sleep(1);

        $response = $this->client->request($options["method"], $this->endpoint."/".$path, $requestOptions);

        return json_decode($response->getBody(), true);
    }
}
