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

namespace gordonmcvey\httpsupport\test\unit;

use gordonmcvey\httpsupport\enum\httpcodes\SuccessCodes;
use gordonmcvey\httpsupport\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    #[Test]
    public function itInstantiatesWithDefaults(): void
    {
        $response = new Response(SuccessCodes::OK, "Hello, world!");

        $this->assertEquals('application/json', $response->header('Content-Type'));
        $this->assertEquals(13, $response->header('Content-Length'));
    }

    #[Test]
    public function itDisallowsManualContentTypeAndLength(): void
    {
        $response = new Response(SuccessCodes::OK, "Hello, world!", [
            "Content-Type"   => "application/octet-stream",
            "Content-Length" => '12345',
        ]);

        $this->assertEquals('application/json', $response->header('Content-Type'));
        $this->assertEquals(13, $response->header('Content-Length'));
    }

    #[Test]
    public function itSetsWithCaseInsensitiveKeys(): void
    {
        $response = new Response(SuccessCodes::OK, "Hello, world!");
        
        $response->setHeader("foo", "bar");
        $this->assertEquals("bar", $response->header("foo"));
        $response->setHeader("FOO", "baz");
        $this->assertEquals("baz", $response->header("foo"));
        $response->setHeader("fOo", "quux");
        $this->assertEquals("quux", $response->header("foo"));
    }

    #[Test]
    public function itPreservesHeaderKeyCase(): void
    {
        $response = new Response(SuccessCodes::OK, "");

        $response->setHeader("lowercase", "Lower-case header")
            ->setHeader("UPPERCASE", "Upper-case header")
            ->setHeader("camelCase", "Camel-case header")
            ->setHeader("mIXEdcAsE", "Mixed-case header");

        $this->assertEquals([
            "Content-Type"   => "application/json",
            "Content-Length" => "0",
            "lowercase"      => "Lower-case header",
            "UPPERCASE"      => "Upper-case header",
            "camelCase"      => "Camel-case header",
            "mIXEdcAsE"      => "Mixed-case header",
        ], $response->headers());
    }

    #[Test]
    public function itInitialisesContentType(): void
    {
        $response = new Response(responseCode: SuccessCodes::OK, body: "Hello, world!", contentType: "text/plain");
        
        $this->assertSame("text/plain", $response->contentType());
        $this->assertNull( $response->contentEncoding());
        $this->assertSame("text/plain", $response->header("content-type"));
    }

    #[Test]
    public function itInitialisesContentTypeWithEncoding(): void
    {
        $response = new Response(responseCode: SuccessCodes::OK, body: "Hello, world!", contentType: "text/plain", encoding: "utf-8");
        
        $this->assertSame("text/plain", $response->contentType());
        $this->assertSame("utf-8", $response->contentEncoding());
        $this->assertSame("text/plain; charset=utf-8", $response->header("content-type"));
    }

    #[Test]
    public function itInitialisesContentLength(): void
    {
        $response = new Response(SuccessCodes::OK, "Hello, world!");

        $this->assertSame(13, $response->contentLength());
    }

    #[Test]
    public function itUpdatesContentLength(): void
    {
        $response = (new Response(SuccessCodes::OK, "Hello, world!"))
            ->setBody("The quick brown fox jumps over the lazy dog.");

        $this->assertSame(44, $response->contentLength());
    }
}
