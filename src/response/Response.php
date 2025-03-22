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

namespace gordonmcvey\httpsupport\response;

use gordonmcvey\httpsupport\enum\statuscodes\ClientErrorCodes;
use gordonmcvey\httpsupport\enum\statuscodes\InfoCodes;
use gordonmcvey\httpsupport\enum\statuscodes\RedirectCodes;
use gordonmcvey\httpsupport\enum\statuscodes\ServerErrorCodes;
use gordonmcvey\httpsupport\enum\statuscodes\SuccessCodes;
use Stringable;

class Response implements ResponseInterface, Stringable
{
    private const string CONTENT_TYPE = "Content-Type";

    private const string CONTENT_LENGTH = "Content-Length";

    private const string DEFAULT_CONTENT_TYPE = "text/plain";

    private const string CONTENT_TYPE_FORMAT = "%s; charset=%s";

    /**
     * According to RFC-7230 header line breaks must be \r\n
     *
     * @link https://datatracker.ietf.org/doc/html/rfc7230#page-19
     */
    private const string NEWLINE = "\r\n";

    /**
     * Map the lower-case version of the array key to the actual header array.  We do this because the HTTP specs say
     * that headers are case-insensitive, and that we must retain the key casing that the user specifies.  However this
     * could lead to us including multiple instances of the same header key with different casing.  In order to avoid
     * that we store a lower-case version of the key and map it to the actual header
     *
     * @var array<string, string>
     */
    private array $headerMap = [];

    private int $contentLength = 0;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private InfoCodes|SuccessCodes|RedirectCodes|ClientErrorCodes|ServerErrorCodes $responseCode,
        private string $body,
        private array $headers = [],
        private string $contentType = self::DEFAULT_CONTENT_TYPE,
        private ?string $encoding = null,
    ) {
        foreach (array_keys($headers) as $key) {
            $this->headerMap[strtolower($key)] = $key;
        }

        $this->contentLength = strlen($body);
        $this->contentTypeHeader();
        $this->contentLengthHeader();
    }

    public function responseCode(): InfoCodes|SuccessCodes|RedirectCodes|ClientErrorCodes|ServerErrorCodes
    {
        return $this->responseCode;
    }

    public function setResponseCode(
        InfoCodes|SuccessCodes|RedirectCodes|ClientErrorCodes|ServerErrorCodes $responseCode,
    ): self {
        $this->responseCode = $responseCode;
        return $this;
    }

    public function header(string $key): ?string
    {
        return $this->headers[$this->headerMap[strtolower($key)]] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    public function setHeader(string $key, string $value): self
    {
        $normalisedKey = strtolower($key);

        if (isset($this->headerMap[$normalisedKey])) {
            unset($this->headers[$this->headerMap[$normalisedKey]]);
        }

        $this->headers[$key] = $value;
        $this->headerMap[$normalisedKey] = $key;

        return $this;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        $this->contentLength = strlen($body);
        $this->contentLengthHeader();
        return $this;
    }

    public function contentType(): string
    {
        return $this->contentType;
    }

    public function contentEncoding(): ?string
    {
        return $this->encoding;
    }

    public function contentLength(): int
    {
        return $this->contentLength;
    }

    public function sendHeaders(): self
    {
        http_response_code($this->responseCode->value);

        foreach ($this->headers as $headerKey => $headerValue) {
            header(sprintf("%s: %s", $headerKey, $headerValue));
        }

        return $this;
    }

    public function __tostring(): string
    {
        // @todo User-configurable HTTP version
        $string = new ResponseStatusHeader(1.1, $this->responseCode) . self::NEWLINE;

        foreach ($this->headers() as $key => $value) {
            $string .= sprintf("%s: %s", $key, $value) . self::NEWLINE;
        }

        $string .= self::NEWLINE . $this->body;

        return $string;
    }

    private function contentTypeHeader(): void
    {
        $contentTypeString = null !== $this->encoding ?
            sprintf(self::CONTENT_TYPE_FORMAT, $this->contentType, $this->encoding) :
            $this->contentType;

        $this->setHeader(self::CONTENT_TYPE, $contentTypeString);
    }

    private function contentLengthHeader(): void
    {
        $this->setHeader(self::CONTENT_LENGTH, (string) $this->contentLength);
    }
}
