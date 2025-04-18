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

namespace gordonmcvey\httpsupport\request;

use gordonmcvey\httpsupport\enum\Verbs;

/**
 * @phpstan-consistent-constructor
 */
class Request implements RequestInterface, \JsonSerializable
{
    private const string REQUEST_BODY_SOURCE = "php://input";
    private const string HEADER_PREFIX = "HTTP_";
    private const string REQUEST_URI = "REQUEST_URI";
    private const string REQUEST_METHOD = "REQUEST_METHOD";
    private const string RAW_HEADER_KEY_SEP = "_";
    private const string COOKED_HEADER_KEY_SEP = "-";

    // Keys in the ServerParams array that don't start with the normal header prefix but which are still headers
    private const array SPECIAL_HEADER_KEYS = [
        "CONTENT_TYPE"   => "CONTENT_TYPE",
        "CONTENT_LENGTH" => "CONTENT_LENGTH",
    ];

    /**
     * Header values (lazy-populated on first call to header() or headers())
     *
     * @var ?array<string, mixed>
     */
    private ?array $headers = null;

    private ?Verbs $verb = null;

    private string|false|null $body = null;

    /**
     * Class constructor
     *
     * For the BodyFactory argument, you can provide either a factory that will extract the body on first invokation
     * (thus allowing lazy evaluation of the request body), the literal body string (as either a string or a Stringable
     * object), or null (if you aren't going to be using the body for the request you're handling)
     *
     * If you pass in a bodyFactory value that is both Callable and Stringable, then it will be treated as a Callable
     *
     * Note that if you pass in a Stringable, it will be evaluated on instantiation, not on the first call to
     * Request::body(), so it is recommended that you don't use Stringables that do a lot of heavy lifting, especially
     * if not all requests will require you to access the request body
     *
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed> $payloadParams
     * @param array<string, mixed> $cookieParams
     * @param array<string, array{
     *     name: string,
     *     type: string,
     *     size: non-negative-int,
     *     tmp_name: string,
     *     error_code: non-negative-int
     * }> $fileParams
     * @param array<string, mixed> $serverParams
     * @param callable|string|null $bodyFactory Data source for the request body (if any)
     * @todo Handle Files
     */
    public function __construct(
        private readonly array $queryParams,
        private readonly array $payloadParams,
        private readonly array $cookieParams,
        private readonly array $fileParams,
        private readonly array $serverParams,
        private readonly mixed $bodyFactory = null,
    ) {
        // We can't enforce callables by type-hinting for some reason that I'm sure must make sense to somebody
        $factoryIsCallable = is_callable($bodyFactory);
        $factoryIsStringable = is_string($bodyFactory) || $bodyFactory instanceof \Stringable;

        if (null !== $bodyFactory && !$factoryIsCallable && !$factoryIsStringable) {
            throw new \TypeError("Body factory must be callable");
        }

        $factoryIsCallable || !$factoryIsStringable || $this->body = (string) $bodyFactory;
    }

    public function headers(): array
    {
        if (null === $this->headers) {
            $this->headers = $this->extractHeaders();
        }

        return $this->headers;
    }

    public function header(string $name, mixed $default = null): mixed
    {
        return $this->headers()[$name] ?? $default;
    }

    public function setHeader(string $name, mixed $value): self
    {
        if (null === $this->headers) {
            $this->headers = $this->extractHeaders();
        }

        $this->headers[$name] = $value;
        return $this;
    }

    public function contentType(): ?string
    {
        return $this->header("Content-Type");
    }

    public function contentLength(): ?int
    {
        $length = $this->header("Content-Length");
        return null !== $length ? (int) $length : null;
    }

    public function uri(): string
    {
        return $this->serverParams[self::REQUEST_URI];
    }

    public function verb(): Verbs
    {
        if (null === $this->verb) {
            $this->verb = Verbs::from($this->serverParam(self::REQUEST_METHOD));
        }

        return $this->verb;
    }

    public function queryParam(string $name, mixed $default = null): mixed
    {
        return $this->queryParams[$name] ?? $default;
    }

    public function payloadParam(string $name, mixed $default = null): mixed
    {
        return $this->payloadParams[$name] ?? $default;
    }

    public function cookieParam(string $name, mixed $default = null): mixed
    {
        return $this->cookieParams[$name] ?? $default;
    }

    public function serverParam(string $name, mixed $default = null): mixed
    {
        return $this->serverParams[$name] ?? $default;
    }

    public function uploadedFiles(): array
    {
        return $this->fileParams;
    }

    public function uploadedFile(string $name): ?array
    {
        return $this->fileParams[$name] ?? null;
    }

    public function body(): ?string
    {
        if (null === $this->body) {
            $this->body = is_callable($this->bodyFactory) ?
                ($this->bodyFactory)() :
                false;
        }

        return $this->body ?: null;
    }

    /**
     * @return array{
     *     queryParams: array<string, mixed>,
     *     postParams: array<string, mixed>,
     *     cookieParams: array<string, mixed>,
     *     fileParams: array<string, array<string, mixed>>,
     *     serverParams: array<string, mixed>
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            "queryParams"   => $this->queryParams,
            "payloadParams" => $this->payloadParams,
            "cookieParams"  => $this->cookieParams,
            "fileParams"    => $this->fileParams,
            "serverParams"  => $this->serverParams,
        ];
    }

    /**
     * This code is based on the V2 JAPI header logic, which in turn seems to be loosely based on a comment from the
     * PHP manual (the getallheaders function is only guaranteed to exist if PHP is running under Apache)
     *
     * @return array<string, mixed>
     * @link https://www.php.net/manual/en/function.getallheaders.php
     */
    private function extractHeaders(): array
    {
        $headers = [];
        $prefixLength = strlen(self::HEADER_PREFIX);

        foreach ($this->serverParams as $key => $value) {
            $isSpecialHeader = isset(self::SPECIAL_HEADER_KEYS[$key]);

            if (0 === strpos($key, self::HEADER_PREFIX) || $isSpecialHeader) {
                $headerKey = str_replace(' ', self::COOKED_HEADER_KEY_SEP, ucwords(
                    strtolower(str_replace(self::RAW_HEADER_KEY_SEP, ' ', $key))
                ));
                if (!$isSpecialHeader) {
                    $headerKey = substr($headerKey, $prefixLength);
                }

                $headers[$headerKey] = $value;
            }
        }

        return $headers;
    }

    /**
     * Factory method to populate a Request instance from the PHP request
     *
     * @param callable|string|null $bodyFactory Data source for the request body (if any)
     */
    public static function fromSuperGlobals(mixed $bodyFactory = null): static
    {
        null !== $bodyFactory || $bodyFactory = static::defaultBodyFactory(...);

        return new static(
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_SERVER,
            $bodyFactory,
        );
    }

    protected static function defaultBodyFactory(): ?string {
        $requestBody = file_get_contents(self::REQUEST_BODY_SOURCE);
        return false !== $requestBody ? $requestBody : null;
    }
}
