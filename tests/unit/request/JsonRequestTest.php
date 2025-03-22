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

use gordonmcvey\httpsupport\request\JsonRequest;
use JsonException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

class JsonRequestTest extends TestCase
{
    #[Test]
    public function itCanParseJson(): void
    {
        $serverParams = ["CONTENT_TYPE" => "application/json"];
        $json = '{"foo": "bar", "baz": "quux"}';

        $request = new JsonRequest([], [], [], [], $serverParams, $json);
        $this->assertEquals(
            (object) [
                "foo" => "bar",
                "baz" => "quux",
            ], 
            $request->jsonBody(),
        );
    }

    #[Test]
    public function itThrowsOnIncorrectContentType(): void
    {
        $serverParams = ["CONTENT_TYPE" => "text/plain"];
        $json = '{"foo": "bar", "baz": "quux"}';

        $this->expectException(ValueError::class);
        new JsonRequest([], [], [], [], $serverParams, $json);
    }

    #[Test]
    public function itDoesntParseInvalidJson(): void
    {
        $serverParams = ["CONTENT_TYPE" => "application/json"];
        $json = '{"foo": "bar", "baz": "quux"';

        $request = new JsonRequest([], [], [], [], $serverParams, $json);
        $this->expectException(JsonException::class);
        $request->jsonBody();
    }

    #[Test]
    public function itCanGetParamFromJson(): void
    {
        $serverParams = ["CONTENT_TYPE" => "application/json"];
        $json = '{"foo": "bar", "baz": "quux"}';

        $request = new JsonRequest([], [], [], [], $serverParams, $json);
        $this->assertSame("bar", $request->jsonParam("foo"));
        $this->assertSame("quux", $request->jsonParam("baz"));
    }

    #[Test]
    public function itCanGetDefaultValueForUnsetJsonParam(): void
    {
        $serverParams = ["CONTENT_TYPE" => "application/json"];
        $json = '{}';

        $request = new JsonRequest([], [], [], [], $serverParams, $json);

        $this->assertSame("bar", $request->jsonParam("foo", "bar"));
        $this->assertNull($request->jsonParam("foo"));
    }

    #[Test]
    public function itInstantiatesFromSuperGlobals(): void
    {
        $_SERVER["CONTENT_TYPE"] = "application/json";
        $request = JsonRequest::fromSuperGlobals();

        $this->assertInstanceOf(JsonRequest::class, $request);
        $this->assertSame(JsonRequest::class, get_class($request));
        $this->assertNull($request->body());
    }
}
