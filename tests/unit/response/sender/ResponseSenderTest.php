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

namespace gordonmcvey\httpsupport\test\unit\response\sender;

use gordonmcvey\httpsupport\enum\statuscodes\ClientErrorCodes;
use gordonmcvey\httpsupport\enum\statuscodes\InfoCodes;
use gordonmcvey\httpsupport\enum\statuscodes\RedirectCodes;
use gordonmcvey\httpsupport\enum\statuscodes\ServerErrorCodes;
use gordonmcvey\httpsupport\enum\statuscodes\SuccessCodes;
use gordonmcvey\httpsupport\interface\response\ResponseInterface;
use gordonmcvey\httpsupport\response\sender\ResponseSender;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ResponseSenderTest extends TestCase
{
    /**
     * headers_list() doesn't work in a commandline environment (like the test runner), so we require an xDebug method
     * that provides equivalent functionality but which does work in the commandline for this test to execute
     *
     * @param array<string, string> $headers
     * @param array{
     *     expectedResponseCode: int,
     *     expectedHeaders: array<string>,
     *     expectedBody: string
     * } $expectations
     * @throws Exception
     */
    #[Test]
    #[RequiresFunction("xdebug_get_headers")]
    #[DataProvider("provideResponses")]
    public function itSendsTheResponse(
        InfoCodes|SuccessCodes|RedirectCodes|ClientErrorCodes|ServerErrorCodes $responseCode,
        array $headers,
        string $body,
        array $expectations
    ): void {
        $response = $this->createMock(ResponseInterface::class);
        $response->method("responseCode")->willReturn($responseCode);
        $response->method("headers")->willReturn($headers);
        $response->method("body")->willReturn($body);

        $sender = new ResponseSender();

        ob_start();

        $sender->send($response);

        $sentHeaders = xdebug_get_headers(); // instead of headers_list()
        $responseCode = http_response_code();
        $sentBody = (string) ob_get_contents();

        ob_end_clean();

        $this->assertSame($expectations["expectedResponseCode"], $responseCode);
        $this->assertEqualsCanonicalizing($expectations["expectedHeaders"], $sentHeaders);
        $this->assertJsonStringEqualsJsonString($expectations["expectedBody"], $sentBody);
    }

    /**
     * @return Iterator<string, array{
     *     responseCode: InfoCodes|SuccessCodes|RedirectCodes|ClientErrorCodes|ServerErrorCodes,
     *     headers: array<string, string>,
     *     body: string,
     *     expectations: array{
     *         expectedResponseCode: int,
     *         expectedHeaders: array<string>,
     *         expectedBody: string
     *     }
     * }>
     */
    public static function provideResponses(): Iterator
    {
        yield "Typical response" => [
            "responseCode"  => SuccessCodes::OK,
            "headers"       => [
                "Content-Type" => "application/json",
                "Accept"       => "application/json",
                "foo"          => "bar",
            ],
            "body"          => (string) json_encode([
                "Fred"   => "Wilma",
                "Barney" => "Betty",
            ]),
            "expectations"  => [
                "expectedResponseCode" => SuccessCodes::OK->value,
                "expectedHeaders"      => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "foo: bar",
                ],
                "expectedBody"         => (string) json_encode([
                    "Fred"   => "Wilma",
                    "Barney" => "Betty",
                ]),
            ],
        ];

        yield "Client error response" => [
            "responseCode"  => ClientErrorCodes::FORBIDDEN,
            "headers"       => [
                "Content-Type" => "application/json",
                "Accept"       => "application/json",
                "foo"          => "bar",
            ],
            "body"          => (string) json_encode([
                "Fred"   => "Wilma",
                "Barney" => "Betty",
            ]),
            "expectations"  => [
                "expectedResponseCode" => ClientErrorCodes::FORBIDDEN->value,
                "expectedHeaders"      => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "foo: bar",
                ],
                "expectedBody"         => (string) json_encode([
                    "Fred"   => "Wilma",
                    "Barney" => "Betty",
                ]),
            ],
        ];

        yield "Server error response" => [
            "responseCode" => ServerErrorCodes::INTERNAL_SERVER_ERROR,
            "headers"      => [
                "Content-Type" => "application/json",
                "Accept"       => "application/json",
                "foo"          => "bar",
            ],
            "body"         => "{}",
            "expectations"  => [
                "expectedResponseCode" => ServerErrorCodes::INTERNAL_SERVER_ERROR->value,
                "expectedHeaders"      => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "foo: bar",
                ],
                "expectedBody"         => "{}",
            ],
        ];
    }
}
