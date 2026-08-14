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
     */
    public function __construct(private array $data)
    {
        $this->id = isset($this->data['id']) ? (string)$this->data['id'] : '';
    }
}
