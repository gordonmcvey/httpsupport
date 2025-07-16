<?php

/**
 * Copyright © 2025 Gordon McVey
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace gordonmcvey\httpsupport\test\unit\request;

use gordonmcvey\httpsupport\enum\Verbs;
use gordonmcvey\httpsupport\request\payload\PayloadHandlerInterface;
use gordonmcvey\httpsupport\request\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ValueError;

class RequestTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function itCanReturnSpecificHeader(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
            "http_header_three"  => "baz",
            "ignore_this_header" => "quux",
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame("foo", $request->header("Header-One"));
        $this->assertSame("bar", $request->header("Header-Two"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanReturnDefaultValueForUnsetHeader(): void
    {
        $request = new Request([], [], [], [], $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame("MyDefault", $request->header("Header-One", "MyDefault"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCannotReturnImproperlyNamedHeader(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
            "http_header_three"  => "baz",
            "ignore_this_header" => "quux",
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));
        $this->assertNull($request->header("Ignore-This-Header"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCannotReturnImproperlyCasedHeader(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
            "http_header_three"  => "baz",
            "ignore_this_header" => "quux",
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));
        $this->assertNull($request->header("Header-Three"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanReturnAllHeaders(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
            "http_header_three"  => "baz",
            "ignore_this_header" => "quux",
        ];
        $expectations = [
            "Header-One" => "foo",
            "Header-Two" => "bar",
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));
        $this->assertEquals($expectations, $request->headers());
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanAddAnHeader(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE" => "foo",
            "HTTP_HEADER_TWO" => "bar",
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));

        $this->assertNull($request->header("Header-Three"));
        $request->setHeader("Header-Three", "baz");
        $this->assertSame("baz", $request->header("Header-Three"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanReplaceHeader(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));

        $this->assertSame("bar", $request->header("Header-Two"));
        $request->setHeader("Header-Two", "baz");
        $this->assertSame("baz", $request->header("Header-Two"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanReturnTheUri(): void
    {
        $serverParams = [
            "REQUEST_URI" => "/foo/bar?baz"
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame("/foo/bar?baz", $request->uri());
    }

    /**
     * @throws Exception
     */
    #[Test]
    #[DataProvider("provideVerbs")]
    public function itCanReturnTheVerb(Verbs $verb): void
    {
        $serverParams = [
            "REQUEST_METHOD" => $verb->value,
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame($verb, $request->verb());
    }

    /**
     * @return array<string, array<string, Verbs>>
     */
    public static function provideVerbs(): array
    {
        $cases = [];

        foreach (Verbs::cases() as $case) {
            $cases[$case->value] = ["verb" => $case];
        }

        return $cases;
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itWillThrowOnInvalidVerb(): void
    {
        $serverParams = [
            "REQUEST_METHOD" => "Farble warble garble"
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));
        $this->expectException(ValueError::class);
        $request->verb();
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanGetParamFromQuery(): void
    {
        $queryParams = [
            "foo" => "bar",
            "baz" => "quux",
        ];

        $request = new Request($queryParams, [], [], [], $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame("bar", $request->queryParam("foo"));
        $this->assertSame("quux", $request->queryParam("baz"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanReturnDefaultValueForUnsetQueryParam(): void
    {
        $request = new Request([], [], [], [], $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame("bar", $request->queryParam("foo", "bar"));
        $this->assertNull($request->queryParam("foo"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanGetParamFromPayload(): void
    {
        $payload = $this->createMock(PayloadHandlerInterface::class);
        $payload->expects($this->any())
            ->method("param")
            ->willReturnMap([
                ["foo", null, "bar"],
                ["baz", null, "quux"],
            ])
        ;

        $request = new Request([], [], [], [], $payload);
        $this->assertSame("bar", $request->payloadParam("foo"));
        $this->assertSame("quux", $request->payloadParam("baz"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanReturnDefaultValueForUnsetPayloadParams(): void
    {
        $payload = $this->createMock(PayloadHandlerInterface::class);
        $payload->expects($this->any())
            ->method("param")
            ->willReturnMap([
                ["foo", "bar", "bar"],
            ])
        ;

        $request = new Request([], [], [], [], $payload);
        $this->assertSame("bar", $request->payloadParam("foo", "bar"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanGetParamFromCookie(): void
    {
        $cookieParams = [
            "foo" => "bar",
            "baz" => "quux",
        ];

        $request = new Request([], $cookieParams, [], [], $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame("bar", $request->cookieParam("foo"));
        $this->assertSame("quux", $request->cookieParam("baz"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanReturnDefaultValueForUnsetCookieParam(): void
    {
        $request = new Request([], [], [], [], $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame("bar", $request->cookieParam("foo", "bar"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanGetParamFromServer(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
            "http_header_three"  => "baz",
            "ignore_this_header" => "quux",
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame("foo", $request->serverParam("HTTP_HEADER_ONE"));
        $this->assertSame("bar", $request->serverParam("HTTP_HEADER_TWO"));
        $this->assertSame("baz", $request->serverParam("http_header_three"));
        $this->assertSame("quux", $request->serverParam("ignore_this_header"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanReturnDefaultValueForUnsetServerParam(): void
    {
        $request = new Request([], [], [], [], $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame("bar", $request->serverParam("foo", "bar"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanGetContentType(): void
    {
        $serverParams = [
            "CONTENT_TYPE" => "application/octet-stream",
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));

        $this->assertSame("application/octet-stream", $request->contentType());
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itWontReturnAContextTypeIfNotSet(): void
    {
        $request = new Request([], [], [], [], $this->createMock(PayloadHandlerInterface::class));
        $this->assertNull($request->contentType());
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanGetContentLength(): void
    {
        $serverParams = [
            "CONTENT_LENGTH" => "123",
        ];

        $request = new Request([], [], [], $serverParams, $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame(123, $request->contentLength());
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itWontReturnAContentLengthIfNotSet(): void
    {
        $request = new Request([], [], [], [], $this->createMock(PayloadHandlerInterface::class));
        $this->assertNull($request->contentLength());
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanGetUploadedFiles(): void
    {
        $fileParams = [
            "file1" => [
                "name" => "foo.bar",
                "type" => "application/octet-stream",
                "size" => 12345,
                "tmp_name" => "asdf",
                "error_code" => 0,
            ],
        ];

        $request = new Request([], [], $fileParams, [], $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame($fileParams, $request->uploadedFiles());
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanGetSpecificUploadedFile(): void
    {
        $fileParams = [
            "file1" => [
                "name" => "foo.bar",
                "type" => "application/octet-stream",
                "size" => 12345,
                "tmp_name" => "asdf",
                "error_code" => 0,
            ],
        ];

        $request = new Request([], [], $fileParams, [], $this->createMock(PayloadHandlerInterface::class));
        $this->assertSame($fileParams["file1"], $request->uploadedFile("file1"));
        $this->assertNull($request->uploadedFile("file2"));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itCanFetchTheBody(): void
    {
        $payload = $this->createMock(PayloadHandlerInterface::class);
        $payload->expects($this->once())
            ->method("body")
            ->willReturn("This is the body")
        ;

        $request = new Request([], [], [], [], $payload);
        $this->assertSame("This is the body", $request->body());
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itReturnsNullIForBodyIfPayloadReturnsNull(): void
    {
        $payload = $this->createMock(PayloadHandlerInterface::class);

        $request = new Request([], [], [], [], $payload);
        $this->assertNull($request->body());
    }

    #[Test]
    public function itInstantiatesFromSuperGlobals(): void
    {
        $request = Request::fromSuperGlobals();

        $this->assertSame(Request::class, get_class($request));
        $this->assertNull($request->body());
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function itInstantiatesFromSuperGlobalsWithPayloadHandler(): void
    {
        $payload = $this->createMock(PayloadHandlerInterface::class);
        $payload
            ->expects($this->once())
            ->method("body")
            ->willReturn(json_encode([
                "foo" => 'bar',
                "baz" => "quux",
            ]))
        ;

        $request = Request::fromSuperGlobals($payload);

        $this->assertSame(Request::class, get_class($request));
        $this->assertSame('{"foo":"bar","baz":"quux"}', $request->body());
    }

    #[Test]
    public function itCanBeCastToString(): void
    {
        $payloadHandler = $this->createMock(PayloadHandlerInterface::class);
        $payloadHandler->method("body")->willReturn("This is the body");

        $expectedRequest = "GET /foo/bar?baz=quux\n"
            . "Header-1: Foo\n"
            . "Header-2: Bar\n\n"
            . "This is the body";

        $request = new Request(
            queryParams: [],
            cookieParams: [],
            fileParams: [],
            serverParams: [
                "REQUEST_URI" => "/foo/bar?baz=quux",
                "REQUEST_METHOD" => "GET",
                "HTTP_HEADER_1" => "Foo",
                "HTTP_HEADER_2" => "Bar",
            ],
            payloadHandler: $payloadHandler,
        );

        // implicit __toString() call
        $this->assertEquals($expectedRequest, $request);
    }
}
