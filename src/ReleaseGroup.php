<?php

namespace MusicBrainz;

/**
 * Represents a MusicBrainz release group
 *
 */
class ReleaseGroup
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $primaryType;
    /**
     * @var array
     */
    public $secondaryTypes = [];
    /**
     * @var Release[]
     */
    private $releases = [];

    /**
     * @param array $data
     * @param MusicBrainz $brainz
     */
    public function __construct(private array $data, private readonly MusicBrainz $brainz)
    {
        $this->id             = isset($this->data['id']) ? (string)$this->data['id'] : '';
        $this->primaryType    = isset($this->data['primary-type']) ? (string)$this->data['primary-type'] : '';
        $this->secondaryTypes = $this->data['secondary-types'] ?? [];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->data['title'];
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->data['score'];
    }

    /**
     * @return Release[]
     */
    public function getReleases()
    {
        if (!empty($this->releases)) {
            return $this->releases;
        }

        foreach ($this->data['releases'] as $release) {
            array_push($this->releases, new Release($release, $this->brainz));
        }

        return $this->releases;
    }
}
