<?php

declare(strict_types=1);

namespace MusicBrainz\Tests\HttpAdapters;

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use MusicBrainz\Exception;
use MusicBrainz\HttpAdapters\Psr18HttpAdapter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

final class Psr18HttpAdapterTest extends TestCase
{
    public function testItSendsAPsr7RequestAndDecodesTheJsonResponse(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::callback(function (RequestInterface $request): bool {
                self::assertSame('GET', $request->getMethod());
                self::assertSame(
                    'https://musicbrainz.example/ws/2/artist/4dbf5678-7a31-406a-abbe-232f8ac2cd63?inc=releases&fmt=json',
                    (string) $request->getUri()
                );
                self::assertSame(['application/json'], $request->getHeader('Accept'));
                self::assertSame(['TestApp/1.0.0 (https://example.test)'], $request->getHeader('User-Agent'));

                return true;
            }))
            ->willReturn(new Response(200, [], '{"id":"4dbf5678-7a31-406a-abbe-232f8ac2cd63"}'));

        $factory = new HttpFactory();
        $adapter = new Psr18HttpAdapter($client, $factory, $factory, 'https://musicbrainz.example/ws/2', 0);

        self::assertSame(
            ['id' => '4dbf5678-7a31-406a-abbe-232f8ac2cd63'],
            $adapter->call(
                'artist/4dbf5678-7a31-406a-abbe-232f8ac2cd63',
                ['inc' => 'releases', 'fmt' => 'json'],
                ['method' => 'GET', 'user-agent' => 'TestApp/1.0.0 (https://example.test)']
            )
        );
    }

    public function testItAddsBasicAuthenticationWhenRequired(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::callback(function (RequestInterface $request): bool {
                self::assertSame(['Basic ' . base64_encode('user:password')], $request->getHeader('Authorization'));

                return true;
            }))
            ->willReturn(new Response(200, [], '{}'));

        $factory = new HttpFactory();
        $adapter = new Psr18HttpAdapter($client, $factory, $factory, null, 0);
        $adapter->call('artist/id', [], [
            'method' => 'GET',
            'user-agent' => 'TestApp/1.0.0 (https://example.test)',
            'user' => 'user',
            'password' => 'password',
        ], true);
    }

    public function testItRejectsMissingAuthenticationCredentials(): void
    {
        $factory = new HttpFactory();
        $adapter = new Psr18HttpAdapter($this->createMock(ClientInterface::class), $factory, $factory, null, 0);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Authentication is required');

        $adapter->call('artist/id', [], ['user-agent' => 'TestApp/1.0.0 (https://example.test)'], true);
    }
}
