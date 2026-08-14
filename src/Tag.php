<?php

namespace MusicBrainz;

/**
 * Represents a MusicBrainz tag object
 * @package MusicBrainz
 */
class Tag
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $score;

    /**
     * @param array $data
     */
    public function __construct(private array $data)
    {
        $this->name  = isset($this->data['name']) ? (string)$this->data['name'] : '';
        $this->score = isset($this->data['score']) ? (string)$this->data['score'] : '';
    }
}
