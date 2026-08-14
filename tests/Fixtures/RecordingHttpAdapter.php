<?php

namespace MusicBrainz\Tests\Fixtures;

use MusicBrainz\HttpAdapters\AbstractHttpAdapter;

final class RecordingHttpAdapter extends AbstractHttpAdapter
{
    /** @var array<int, array{path: mixed, params: array, options: array, authRequired: mixed, returnArray: mixed}> */
    public array $calls = [];

    /** @var array<string, mixed> */
    public array $response = [];

    public function call($path, array $params = [], array $options = [], $isAuthRequired = false, $returnArray = false)
    {
        $this->calls[] = [
            'path' => $path,
            'params' => $params,
            'options' => $options,
            'authRequired' => $isAuthRequired,
            'returnArray' => $returnArray,
        ];

        return $this->response;
    }
}
