<?php

namespace MusicBrainz\Tests;

use MusicBrainz\MusicBrainz;
use MusicBrainz\Tests\Fixtures\RecordingHttpAdapter;

#[\PHPUnit\Framework\Attributes\CoversClass(MusicBrainz::class)]
class MusicBrainzTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MusicBrainz\MusicBrainz
     */
    protected $brainz;

    public function setUp(): void
    {
        $this->brainz = new MusicBrainz(new RecordingHttpAdapter());
    }

    public function testLookupBuildsTheExpectedAdapterCall(): void
    {
        $adapter = new RecordingHttpAdapter();
        $adapter->response = ['id' => '4dbf5678-7a31-406a-abbe-232f8ac2cd63'];
        $brainz = new MusicBrainz($adapter);
        $brainz->setUserAgent('TestApp', '1.0.0', 'https://example.test');

        $response = $brainz->lookup(
            'artist',
            '4dbf5678-7a31-406a-abbe-232f8ac2cd63',
            ['releases']
        );

        self::assertSame($adapter->response, $response);
        self::assertSame([
            'path' => 'artist/4dbf5678-7a31-406a-abbe-232f8ac2cd63',
            'params' => ['inc' => 'releases', 'fmt' => 'json'],
            'options' => [
                'method' => 'GET',
                'user-agent' => 'TestApp/1.0.0 (https://example.test)',
                'user' => null,
                'password' => null,
            ],
            'authRequired' => false,
            'returnArray' => false,
        ], $adapter->calls[0]);
    }

    public function testLookupMarksUserTagIncludesAsAuthenticated(): void
    {
        $adapter = new RecordingHttpAdapter();
        $brainz = new MusicBrainz($adapter);

        $brainz->lookup('artist', '4dbf5678-7a31-406a-abbe-232f8ac2cd63', ['user-tags']);

        self::assertTrue($adapter->calls[0]['authRequired']);
    }

    public function testLookupRejectsUnknownIncludes(): void
    {
        $brainz = new MusicBrainz(new RecordingHttpAdapter());

        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('not-an-include is not a valid include');

        $brainz->lookup('artist', '4dbf5678-7a31-406a-abbe-232f8ac2cd63', ['not-an-include']);
    }

    public static function mbidProvider(): array
    {
        return [
            [true, '4dbf5678-7a31-406a-abbe-232f8ac2cd63'],
            [true, '4dbf5678-7a31-406a-abbe-232f8ac2cd63'],
            [false, '4dbf5678-7a314-06aabb-e232f-8ac2cd63'], // invalid spacing for UUID's
            [false, '4dbf5678-7a31-406a-abbe-232f8az2cd63'], // z is an invalid character
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('mbidProvider')]
    public function testIsValidMBID(bool $validation, string $mbid): void
    {
        $this->assertSame($validation, $this->brainz->isValidMBID($mbid));
    }
}
