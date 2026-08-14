<?php

namespace MusicBrainz;

/**
 * Represents a MusicBrainz collection object
 * @package MusicBrainz
 */
class Collection
{
    /**
     * @var string
     */
    public $id;

    /**
     * @param array $data
     * @param MusicBrainz $brainz
     */
    public function __construct(private array $data, private readonly MusicBrainz $brainz)
    {
        $this->id = isset($this->data['id']) ? (string)$this->data['id'] : '';
    }
}
