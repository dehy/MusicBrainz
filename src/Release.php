<?php

namespace MusicBrainz;

/**
 * Represents a MusicBrainz release object
 * @package MusicBrainz
 */
class Release
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $status;
    /**
     * @var string
     */
    public $quality;
    /**
     * @var string
     */
    public $language;
    /**
     * @var string
     */
    public $script;
    /**
     * @var string
     */
    public $date;
    /**
     * @var string
     */
    public $country;
    /**
     * @var string
     */
    public $barcode;
    /**
     * @var Artist[]
     */
    public $artists = [];
    /**
     * @var ReleaseGroup
     */
    public $releaseGroup;
    /**
     * @var
     */
    protected $releaseDate;

    /**
     * @param array $data
     * @param MusicBrainz $brainz
     */
    public function __construct(private array $data, private readonly MusicBrainz $brainz)
    {
        $this->id       = isset($this->data['id']) ? (string)$this->data['id'] : '';
        $this->title    = isset($this->data['title']) ? (string)$this->data['title'] : '';
        $this->status   = isset($this->data['status']) ? (string)$this->data['status'] : '';
        $this->quality  = isset($this->data['quality']) ? (string)$this->data['quality'] : '';
        $this->language = isset($this->data['text-representation']['language']) ? (string)$this->data['text-representation']['language'] : '';
        $this->script   = isset($this->data['text-representation']['script']) ? (string)$this->data['text-representation']['script'] : '';
        $this->date     = isset($this->data['date']) ? (string)$this->data['date'] : '';
        $this->country  = isset($this->data['country']) ? (string)$this->data['country'] : '';
        $this->barcode  = isset($this->data['barcode']) ? (string)$this->data['barcode'] : '';

        if (isset($this->data['artist-credit'])) {
            $this->setArtists($this->data['artist-credit']);
        }

        if (isset($this->data['release-group'])) {
            $this->setReleaseGroup(new ReleaseGroup($this->data['release-group'], $this->brainz));
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param ReleaseGroup $releaseGroup
     *
     * @return $this
     */
    public function setReleaseGroup(ReleaseGroup $releaseGroup)
    {
        $this->releaseGroup = $releaseGroup;

        return $this;
    }

    /**
     * Get's the earliest release date
     * @return \DateTime
     */
    public function getReleaseDate()
    {
        if (null != $this->releaseDate) {
            return $this->releaseDate;
        }

        // If there is no release date set, look through the release events
        if (!isset($this->data['date']) && isset($this->data['release-events'])) {
            return $this->getReleaseEventDates($this->data['release-events']);
        } elseif (isset($this->data['date'])) {
            if (preg_match("/^\d{4}$/", $this->data['date'])) {
                return \DateTime::createFromFormat('Y', $this->data['date']);
            }
            return new \DateTime($this->data['date']);
        }

        return new \DateTime();
    }

    /**
     * @param array $releaseEvents
     *
     * @return array
     */
    public function getReleaseEventDates(array $releaseEvents)
    {

        $releaseDate = new \DateTime();

        foreach ($releaseEvents as $releaseEvent) {
            if (isset($releaseEvent['date'])) {
                $releaseDateTmp = new \DateTime($releaseEvent['date']);

                if ($releaseDateTmp < $releaseDate) {
                    $releaseDate = $releaseDateTmp;
                }
            }
        }

        return $releaseDate;
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
        if (!$this->artists) {
            $includes = [
                'artists',
            ];

            $release = $this->brainz->lookup('release', $this->getId(), $includes);
            $this->setArtists([$release['artist-credit']]);
        }
        return ($this->artists ? $this->artists[0] : null);
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

            $release = $this->brainz->lookup('release', $this->getId(), $includes);
            $this->setArtists($release['artist-credit']);
        }
        return $this->artists;
    }
}
