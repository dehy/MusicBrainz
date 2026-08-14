<?php

namespace MusicBrainz;

/**
 * Represents a MusicBrainz Recording object
 * @package MusicBrainz
 */
class Recording
{
    private int $length = 0;

    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $title;
    /**
     * @var int
     */
    public $score;
    /**
     * @var Artist[]
     */
    public $artists = [];
    /**
     * @var Release[]
     */
    public $releases = [];

    /**
     * @param array $data
     * @param MusicBrainz $brainz
     */
    public function __construct(private array $data, private readonly MusicBrainz $brainz)
    {
        $this->id       = (string)$this->data['id'];
        $this->title    = (string)$this->data['title'];
        $this->length   = (isset($this->data['length'])) ? (int)$this->data['length'] : 0;
        $this->score    = (isset($this->data['score'])) ? (int)$this->data['score'] : 0;

        if (isset($this->data['artist-credit'])) {
            $this->setArtists($this->data['artist-credit']);
        }

        if (isset($this->data['releases'])) {
            $this->setReleases($this->data['releases']);
        }
    }

    /**
     * @param array $releases
     *
     * @return $this
     */
    public function setReleases(array $releases)
    {
        foreach ($releases as $release) {
            array_push($this->releases, new Release($release, $this->brainz));
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @throws Exception
     * @return array
     */
    public function getReleaseDates()
    {

        if (empty($this->releases)) {
            throw new Exception('Could not find any releases in the recording');
        }

        $releaseDates = [];

        foreach ($this->releases as $release) {
            /** @var Release $release */
            array_push($releaseDates, $release->getReleaseDate());
        }

        asort($releaseDates);

        return $releaseDates;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $artists
     *
     * @return $this
     */
    public function setArtists(array $artists)
    {
        foreach ($artists as $artist) {
            array_push($this->artists, new Artist($artist["artist"], $this->brainz));
        }

        return $this;
    }

    /**
     * @return Artist
     */
    public function getArtist()
    {
        return ($this->getArtists() ? $this->getArtists()[0] : null);
    }

    /**
     * @return Artist[]
     */
    public function getArtists()
    {
        if (!$this->artists) {
            $includes = [
                'artists',
            ];

            $release = $this->brainz->lookup('recording', $this->getId(), $includes);
            $this->setArtists($release['artist-credit']);
        }
        return $this->artists;
    }

    /**
     * @param string $format
     *
     * @return int|string
     */
    public function getLength($format = 'int')
    {
        return match ($format) {
            'short' => str_replace('.', ':', number_format(($this->length / 1000 / 60), 2)),
            'long' => str_replace('.', 'm ', number_format(($this->length / 1000 / 60), 2)) . 's',
            default => $this->length,
        };
    }
}
