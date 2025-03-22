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
use gordonmcvey\httpsupport\request\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Stringable;

class RequestTest extends TestCase
{
    #[Test]
    public function itCanReturnSpecificHeader(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
            "http_header_three"  => "baz",
            "ignore_this_header" => "quux",
        ];

        $request = new Request([], [], [], [], $serverParams, null);
        $this->assertSame("foo", $request->header("Header-One"));
        $this->assertSame("bar", $request->header("Header-Two"));
    }

    #[Test]
    public function itCanReturnDefaultValueForUnsetHeader(): void
    {
        $request = new Request([], [], [], [], [], null);
        $this->assertSame("MyDefault", $request->header("Header-One", "MyDefault"));
    }

    #[Test]
    public function itcannotReturnImproperlyNamedHeader(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
            "http_header_three"  => "baz",
            "ignore_this_header" => "quux",
        ];

        $request = new Request([], [], [], [], $serverParams, null);
        $this->assertNull($request->header("Ignore-This-Header"));
    }

    #[Test]
    public function itcannotReturnImproperlyCasedHeader(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
            "http_header_three"  => "baz",
            "ignore_this_header" => "quux",
        ];

        $request = new Request([], [], [], [], $serverParams, null);
        $this->assertNull($request->header("Header-Three"));
    }

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

        $request = new Request([], [], [], [], $serverParams, null);
        $this->assertEquals($expectations, $request->headers());
    }

    #[Test]
    public function itCanAddAnHeader(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE" => "foo",
            "HTTP_HEADER_TWO" => "bar",
        ];

        $request = new Request([], [], [], [], $serverParams, null);

        $this->assertNull($request->header("Header-Three"));
        $request->setHeader("Header-Three", "baz");
        $this->assertSame("baz", $request->header("Header-Three"));
    }

    #[Test]
    public function itCanReplaceHeader(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
        ];

        $request = new Request([], [], [], [], $serverParams, null);

        $this->assertSame("bar", $request->header("Header-Two"));
        $request->setHeader("Header-Two", "baz");
        $this->assertSame("baz", $request->header("Header-Two"));
    }

    #[Test]
    public function itCanReturnTheUri(): void
    {
        $serverParams = [
            "REQUEST_URI" => "/foo/bar?baz"
        ];

        $request = new Request([], [], [], [], $serverParams, null);
        $this->assertSame("/foo/bar?baz", $request->uri());
    }

    #[Test]
    #[DataProvider("provideVerbs")]
    public function itCanReturnTheVerb(Verbs $verb): void
    {
        $serverParams = [
            "REQUEST_METHOD" => $verb->value,
        ];

        $request = new Request([], [], [], [], $serverParams, null);
        $this->assertSame($verb, $request->verb());
    }

    public static function provideVerbs(): array
    {
        $cases = [];

        foreach (Verbs::cases() as $case) {
            $cases[$case->value] = ["verb" => $case];
        }

        return $cases;
    }

    #[Test]
    public function itWillThrowOnInvalidVerb(): void
    {
        $serverParams = [
            "REQUEST_METHOD" => "Farble warble garble"
        ];

        $request = new Request([], [], [], [], $serverParams, null);
        $this->expectException(\ValueError::class);
        $request->verb();
    }

    #[Test]
    public function itCanGetParamFromQuery(): void
    {
        $queryParams = [
            "foo" => "bar",
            "baz" => "quux",
        ];

        $request = new Request($queryParams, [], [], [], [], null);
        $this->assertSame("bar", $request->param("foo"));
        $this->assertSame("quux", $request->param("baz"));
        $this->assertSame("bar", $request->queryParam("foo"));
        $this->assertSame("quux", $request->queryParam("baz"));
    }

    #[Test]
    public function itCanReturnDefaultValueForUnsetQueryParam(): void
    {
        $request = new Request([], [], [], [], [], null);
        $this->assertSame("bar", $request->param("foo", "bar"));
        $this->assertSame("bar", $request->queryParam("foo", "bar"));
        $this->assertNull($request->param("foo",));
        $this->assertNull($request->queryParam("foo"));
    }

    #[Test]
    public function itCanGetParamFromPost(): void
    {
        $postParams = [
            "foo" => "bar",
            "baz" => "quux",
        ];

        $request = new Request([], $postParams, [], [], [], null);
        $this->assertSame("bar", $request->param("foo"));
        $this->assertSame("quux", $request->param("baz"));
        $this->assertSame("bar", $request->postParam("foo"));
        $this->assertSame("quux", $request->postParam("baz"));
    }

    #[Test]
    public function itCanReturnDefaultValueForUnsetPostParam(): void
    {
        $request = new Request([], [], [], [], [], null);
        $this->assertSame("bar", $request->param("foo", "bar"));
        $this->assertSame("bar", $request->postParam("foo", "bar"));
    }

    #[Test]
    public function itCanGetParamFromCookie(): void
    {
        $cookieParams = [
            "foo" => "bar",
            "baz" => "quux",
        ];

        $request = new Request([], [], $cookieParams, [], [], null);
        $this->assertSame("bar", $request->cookieParam("foo"));
        $this->assertSame("quux", $request->cookieParam("baz"));

        // Cookies shouldn't end up in general params
        $this->assertNull($request->param("foo"));
        $this->assertNull($request->param("baz"));
    }

    #[Test]
    public function itCanReturnDefaultValueForUnsetCookieParam(): void
    {
        $request = new Request([], [], [], [], [], null);

        $this->assertSame("bar", $request->param("foo", "bar"));
        $this->assertSame("bar", $request->cookieParam("foo", "bar"));
    }

    #[Test]
    public function itCanGetParamFromServer(): void
    {
        $serverParams = [
            "HTTP_HEADER_ONE"    => "foo",
            "HTTP_HEADER_TWO"    => "bar",
            "http_header_three"  => "baz",
            "ignore_this_header" => "quux",
        ];

        $request = new Request([], [], [], [], $serverParams, null);
        $this->assertSame("foo", $request->serverParam("HTTP_HEADER_ONE"));
        $this->assertSame("bar", $request->serverParam("HTTP_HEADER_TWO"));
        $this->assertSame("baz", $request->serverParam("http_header_three"));
        $this->assertSame("quux", $request->serverParam("ignore_this_header"));
    }

    #[Test]
    public function itCanReturnDefaultValueForUnsetServerParam(): void
    {
        $request = new Request([], [], [], [], [], null);
        $this->assertSame("bar", $request->serverParam("foo", "bar"));
    }

    #[Test]
    public function itCanGetParamsFromTheCorrectSource(): void
    {
        $queryParams = [
            "param1" => "queryValue1",
            "param4" => "queryValue4",
        ];
        $postParams = [
            "param1" => "postValue1",
            "param2" => "postValue2",
            "param5" => "postValue5",
        ];
        // Cookies should have no bearing on param()
        $cookieParams = [
            "param1" => "cookieValue1",
            "param2" => "cookieValue2",
            "param3" => "cookieValue3",
            "param6" => "cookieValue6",
        ];

        $request = new Request(
            $queryParams,
            $postParams,
            $cookieParams,
            [],
            [],
            null,
        );
        $this->assertSame("queryValue1", $request->param("param1", "default"));
        $this->assertSame("postValue2", $request->param("param2", "default"));
        $this->assertSame("default", $request->param("param3", "default"));
        $this->assertSame("queryValue4", $request->param("param4", "default"));
        $this->assertSame("postValue5", $request->param("param5", "default"));
        $this->assertSame("default", $request->param("param6", "default"));
        $this->assertSame("default", $request->param("param7", "default"));
    }

    #[Test]
    public function itCanGetContentType(): void
    {
        $serverParams = [
            "CONTENT_TYPE" => "application/octet-stream",
        ];

        $request = new Request([], [], [], [], $serverParams);

        $this->assertSame("application/octet-stream", $request->contentType());
    }

    #[Test]
    public function itWontReturnAContextTypeIfNotSet(): void
    {
        $request = new Request([], [], [], [], []);

        $this->assertNull($request->contentType());
    }

    #[Test]
    public function itCanGetContentLength(): void
    {
        $serverParams = [
            "CONTENT_LENGTH" => "123",
        ];

        $request = new Request([], [], [], [], $serverParams);

        $this->assertSame(123, $request->contentLength());
    }

    #[Test]
    public function itWontReturnAContentLengthIfNotSet(): void
    {
        $request = new Request([], [], [], [], []);

        $this->assertNull($request->contentLength());
    }

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

        $request = new Request([], [], [], $fileParams, [], null);
        $this->assertSame($fileParams, $request->uploadedFiles());
    }

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

        $request = new Request([], [], [], $fileParams, [], null);
        $this->assertSame($fileParams["file1"], $request->uploadedFile("file1"));
        $this->assertNull($request->uploadedFile("file2"));
    }

    #[Test]
    public function itCanGetBodyWithFactoryFunction(): void
    {
        $bodyFactory = function(): ?string {
            return "This is the body";
        };

        $request = new Request([], [], [], [], [], $bodyFactory);
        $this->assertSame("This is the body", $request->body());
    }

    #[Test]
    public function itCanGetBodyWithFactoryClass(): void
    {
        $bodyFactory = new class {
            public function __invoke(): ?string {
                return "This is the body";
            }
        };

        $request = new Request([], [], [], [], [], $bodyFactory);
        $this->assertSame("This is the body", $request->body());
    }

    #[Test]
    public function itReturnsNullIForBodyIfFactoryReturnsNull(): void
    {
        $bodyFactory = function(): ?string {
            return null;
        };

        $request = new Request([], [], [], [], [], $bodyFactory);
        $this->assertNull($request->body());
    }

    #[Test]
    public function itReturnsNullIfNoBodyFactoryProvided(): void
    {
        $request = new Request([], [], [], [], [], null);
        $this->assertNull($request->body());
    }

    #[Test]
    public function itWillThrowOnInvalidBodyFactoryType(): void
    {
        $bodyFactory = 12345;

        $this->expectException(\TypeError::class);
        // @phpstan-ignore argument.type
        $request = new Request([], [], [], [], [], $bodyFactory);
    }

    #[Test]
    public function itWillThrowOnNonCallableBodyFactoryClass(): void
    {
        $bodyFactory = new class {
            public function toString(): ?string {
                return "This is the body";
            }
        };

        $this->expectException(\TypeError::class);
        // @phpstan-ignore argument.type
        $request = new Request([], [], [], [], [], $bodyFactory);
    }

    #[Test]
    public function itCanGetStringLiteralBody(): void
    {
        $bodyFactory = "This is the body";

        $request = new Request([], [], [], [], [], $bodyFactory);
        $this->assertSame("This is the body", $request->body());
    }

    #[Test]
    public function itCanGetStringalbeBody(): void
    {
        $bodyFactory = new class {
            public function __toString(): string {
                return "This is the body";
            }
        };

        $request = new Request([], [], [], [], [], $bodyFactory);
        $this->assertSame("This is the body", $request->body());
    }

    #[Test]
    public function itUsesCallableInPriorityToStringableToGetTheBody(): void
    {
        $bodyFactory = new class implements Stringable{
            public function __invoke(): ?string {
                return "This is the body as generated by __invoke";
            }

            public function __tostring(): string {
                return "This is the body as generated by __toString";
            }
        };

        $request = new Request([], [], [], [], [], $bodyFactory);
        $this->assertSame("This is the body as generated by __invoke", $request->body());
    }

    #[Test]
    public function itInstantiatesFromSuperGlobals(): void
    {
        $request = Request::fromSuperGlobals();
        $this->assertInstanceOf(Request::class, $request);
        $this->assertSame(Request::class, get_class($request));
    }
}
