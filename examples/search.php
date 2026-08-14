<pre><?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use MusicBrainz\Filters\ArtistFilter;
use MusicBrainz\Filters\LabelFilter;
use MusicBrainz\Filters\RecordingFilter;
use MusicBrainz\Filters\ReleaseGroupFilter;
use MusicBrainz\HttpAdapters\Psr18HttpAdapter;
use MusicBrainz\MusicBrainz;

require dirname(__DIR__) . '/vendor/autoload.php';

// Create new MusicBrainz object
$factory = new HttpFactory();
$brainz = new MusicBrainz(new Psr18HttpAdapter(new Client(), $factory, $factory));
$brainz->setUserAgent('ApplicationName', '0.2', 'http://example.com');

/**
 * Get the release groups for an artist
 * @see http://musicbrainz.org/doc/Release_Group
 */
$args = [
    "artist" => 'Weezer'
];
try {
    $releaseGroups = $brainz->search(new ReleaseGroupFilter($args));
    var_dump($releaseGroups);
} catch (Exception $e) {
    print $e->getMessage();
}
print "\n\n";


/**
 * Do an artist search and return a list of artists that match
 * a search
 * @see http://musicbrainz.org/doc/Artist
 */
$args = [
    "artist" => 'Weezer'
];
try {
    $artists = $brainz->search(new ArtistFilter($args));
    print_r($artists);
} catch (Exception $e) {
    print $e->getMessage();
}
print "\n\n";

/**
 * Do a recording (song) search
 * @see http://musicbrainz.org/doc/Recording
 */
$args = [
    "recording"  => "Buddy Holly",
    "artist"     => 'Weezer',
    "creditname" => 'Weezer',
    "status"     => 'Official'
];
try {
    $recordings = $brainz->search(new RecordingFilter($args));
    print_r($recordings);
} catch (Exception $e) {
    print $e->getMessage();
}
print "\n\n";


/**
 * Do a search for a label
 * @see http://musicbrainz.org/doc/Label
 */
$args = [
    "label" => "Devils"
];
try {
    $labels = $brainz->search(new LabelFilter($args));
    print_r($labels);
} catch (Exception $e) {
    print $e->getMessage();
}
