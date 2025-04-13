<?php

declare(strict_types=1);

namespace gordonmcvey\httpsupport\test\unit\request\psr7;

use gordonmcvey\httpsupport\enum\Verbs;
use gordonmcvey\httpsupport\request\psr7\ServerRequestAdaptor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class ServerRequestAdaptorTest extends TestCase
{
    #[Test]
    public function itCanReturnSpecificHeader(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->exactly(2))
            ->method("getHeader")
            ->willReturnMap([
                ["Header-One", ["foo"]],
                ["Header-Two", ["bar"]],
            ])
        ;

        $request = new ServerRequestAdaptor($psrRequest);

        $this->assertSame("foo", $request->header("Header-One"));
        $this->assertSame("bar", $request->header("Header-Two"));
    }

    #[Test]
    public function itCanReturnDefaultValueForUnsetHeader(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $request = new ServerRequestAdaptor($psrRequest);

        $this->assertSame("MyDefault", $request->header("Header-One", "MyDefault"));
    }

    #[Test]
    public function itCanReturnAllHeaders(): void
    {
        $expectations = [
            "Header-One" => ["foo"],
            "Header-Two" => ["bar"],
        ];

        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getHeaders")
            ->willReturn([
                "Header-One" => ["foo"],
                "Header-Two" => ["bar"],
            ])
        ;

        $request = new ServerRequestAdaptor($psrRequest);

        $this->assertEquals($expectations, $request->headers());
    }

    #[Test]
    public function itCanAddAnHeader(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("withHeader")
            ->with("Header-Three", "baz")
            ->willReturnSelf()
        ;

        $request = new ServerRequestAdaptor($psrRequest);
        $request->setHeader("Header-Three", "baz");
    }

    #[Test]
    public function itCanReturnTheUri(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method(constraint: "getRequestTarget")
            ->willReturn("/foo/bar?baz");
        ;

        $request = new ServerRequestAdaptor($psrRequest);
        $this->assertSame("/foo/bar?baz", $request->uri());
    }

    #[Test]
    #[DataProvider("provideVerbs")]
    public function itCanReturnTheVerb(Verbs $verb): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->method("getMethod")
            ->willReturn($verb->value);
        ;

        $request = new ServerRequestAdaptor($psrRequest);
        $this->assertSame($verb, $request->verb());
    }

    public static function provideVerbs(): iterable
    {
        foreach (Verbs::cases() as $case) {
            yield $case->value => [
                "verb" => $case
            ];
        }
    }

    #[Test]
    public function itWillThrowOnInvalidVerb(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->method("getMethod")
            ->willReturn("farble warble garble");
        ;

        $request = new ServerRequestAdaptor($psrRequest);
        $this->expectException(\ValueError::class);
        $request->verb();
    }

    #[Test]
    public function itCanGetParamFromQuery(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getQueryParams")
            ->willReturn([
                "foo" => "bar",
                "baz" => "quux",
            ])
        ;

        $request = new ServerRequestAdaptor($psrRequest);

        $this->assertSame("bar", $request->queryParam("foo"));
        $this->assertSame("quux", $request->queryParam("baz"));
    }

    #[Test]
    public function itCanReturnDefaultValueForUnsetQueryParam(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getQueryParams")
            ->willReturn([])
        ;

        $request = new ServerRequestAdaptor($psrRequest);

        $this->assertSame("bar", $request->queryParam("foo", "bar"));
        $this->assertNull(actual: $request->queryParam("foo"));
    }

    #[Test]
    public function itCanGetParamFromPayload(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getParsedBody")
            ->willReturn([
                "foo" => "bar",
                "baz" => "quux",
            ])
        ;

        $request = new ServerRequestAdaptor($psrRequest);
        $this->assertSame("bar", $request->postParam("foo"));
        $this->assertSame("quux", $request->postParam("baz"));
    }

    #[Test]
    public function itCanReturnDefaultValueForUnsetPayloadParam(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getParsedBody")
            ->willReturn([])
        ;

        $request = new ServerRequestAdaptor($psrRequest);
        $this->assertSame("bar", $request->postParam("foo", "bar"));
        $this->assertNull(actual: $request->postParam("foo"));
    }

    #[Test]
    public function itCanGetParamFromCookie(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getCookieParams")
            ->willReturn([
                "foo" => "bar",
                "baz" => "quux",
            ])
        ;

        $request = new ServerRequestAdaptor($psrRequest);
        $this->assertSame("bar", $request->cookieParam("foo"));
        $this->assertSame("quux", $request->cookieParam("baz"));
    }

    #[Test]
    public function itCanReturnDefaultValueForUnsetCookieParam(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getCookieParams")
            ->willReturn([])
        ;

        $request = new ServerRequestAdaptor($psrRequest);
        $this->assertSame("bar", $request->cookieParam("foo", "bar"));
        $this->assertNull(actual: $request->cookieParam("foo"));
    }

    #[Test]
    public function itCanGetParamFromServer(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getServerParams")
            ->willReturn([
                "HTTP_HEADER_ONE"    => "foo",
                "HTTP_HEADER_TWO"    => "bar",
                "http_header_three"  => "baz",
                "ignore_this_header" => "quux",
            ])
        ;

        $request = new ServerRequestAdaptor($psrRequest);
        $this->assertSame("foo", $request->serverParam("HTTP_HEADER_ONE"));
        $this->assertSame("bar", $request->serverParam("HTTP_HEADER_TWO"));
        $this->assertSame("baz", $request->serverParam("http_header_three"));
        $this->assertSame("quux", $request->serverParam("ignore_this_header"));
    }

    #[Test]
    public function itCanReturnDefaultValueForUnsetServerParam(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getServerParams")
            ->willReturn([])
        ;

        $request = new ServerRequestAdaptor($psrRequest);

        $this->assertSame("bar", $request->serverParam("foo", "bar"));
        $this->assertNull(actual: $request->serverParam("foo"));
    }

    #[Test]
    public function itCanGetContentType(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getHeader")
            ->with("Content-Type")
            ->willReturn(["application/octet-stream"])
        ;

        $request = new ServerRequestAdaptor($psrRequest);

        $this->assertSame("application/octet-stream", $request->contentType());
    }

    #[Test]
    public function itWontReturnAContextTypeIfNotSet(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getHeader")
            ->with("Content-Type")
            ->willReturn([])
        ;

        $request = new ServerRequestAdaptor($psrRequest);

        $this->assertNull($request->contentType());
    }

    #[Test]
    public function itCanGetContentLength(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getHeader")
            ->with("Content-Length")
            ->willReturn(["123"])
        ;

        $request = new ServerRequestAdaptor($psrRequest);

        $this->assertSame(123, $request->contentLength());
    }

    #[Test]
    public function itWontReturnAContentLengthIfNotSet(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $psrRequest
            ->expects($this->once())
            ->method("getHeader")
            ->with("Content-Length")
            ->willReturn([])
        ;

        $request = new ServerRequestAdaptor($psrRequest);

        $this->assertNull($request->contentLength());
    }

    #[Test]
    public function itCanGetUploadedFiles(): void
    {
        $this->markTestSkipped("File support not implemented yet");
    }

    #[Test]
    public function itCanGetSpecificUploadedFile(): void
    {
        $this->markTestSkipped("File support not implemented yet");
    }

    #[Test]
    public function itCanGetBody(): void
    {
        $psrRequest = $this->createMock(ServerRequestInterface::class);
        $body = $this->createMock(StreamInterface::class);

        $psrRequest
            ->expects($this->once())
            ->method("getBody")
            ->willReturn($body)
        ;

        $body
            ->expects($this->once())
            ->method("getContents")
            ->willReturn("This is the body")
        ;

        $request = new ServerRequestAdaptor($psrRequest);
        $this->assertSame("This is the body", $request->body());
    }
}
