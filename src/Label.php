<?php

namespace MusicBrainz;

/**
 * Represents a MusicBrainz label object
 */
class Label
{
    private readonly string $type;

    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var array
     */
    public $aliases;
    /**
     * @var int
     */
    public $score;
    /**
     * @var string
     */
    public $sortName;
    /**
     * @var string
     */
    public $country;

    /**
     * @param array $data
     */
    public function __construct(private array $data)
    {
        $this->id       = isset($this->data['id']) ? (string)$this->data['id'] : '';
        $this->type     = isset($this->data['type']) ? (string)$this->data['type'] : '';
        $this->score    = isset($this->data['score']) ? (int)$this->data['score'] : 0;
        $this->sortName = isset($this->data['sort-name']) ? (string)$this->data['sort-name'] : '';
        $this->name     = isset($this->data['name']) ? (string)$this->data['name'] : '';
        $this->country  = isset($this->data['country']) ? (string)$this->data['country'] : '';
        $this->aliases  = $this->data['aliases'] ?? [];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
