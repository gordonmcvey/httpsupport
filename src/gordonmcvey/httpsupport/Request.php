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

namespace gordonmcvey\httpsupport;

use gordonmcvey\httpsupport\enum\Verbs;

class Request implements RequestInterface, \JsonSerializable
{
    private const string REQUEST_BODY_SOURCE = "php://input";
    private const string HEADER_PREFIX = "HTTP_";
    private const string REQUEST_METHOD = "REQUEST_METHOD";

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
     * @param array<string, mixed> $postParams
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
        private readonly array $postParams,
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

    public function verb(): Verbs
    {
        if (null === $this->verb) {
            $this->verb = Verbs::from($this->serverParam(self::REQUEST_METHOD));
        }

        return $this->verb;
    }

    public function param(string $name, mixed $default = null): mixed
    {
        // We deliberately don't include Cookie params in this search because it would raise similar sexurity concerns to those
        // that can happen with the $_REQUEST superglobal
        return $this->queryParams[$name] ?? $this->postParams[$name] ?? $default;
    }

    public function queryParam(string $name, mixed $default = null): mixed
    {
        return $this->queryParams[$name] ?? $default;
    }

    public function postParam(string $name, mixed $default = null): mixed
    {
        return $this->postParams[$name] ?? $default;
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
     *     requestParams: array<string, mixed>,
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
            "postParams"    => $this->postParams,
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

        foreach ($this->serverParams as $key => $value) {
            if (0 === strpos($key, self::HEADER_PREFIX)) {
                $headers[
                    str_replace(' ', '-', ucwords(
                        strtolower(str_replace('_', ' ', substr($key, 5)))
                    ))
                ] = $value;
            }
        }

        return $headers;
    }

    /**
     * Factory method to populate a Request instance from the PHP request
     */
    public static function fromSuperGlobals(): self
    {
        return new self(
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_SERVER,
            function (): ?string {
                $requestBody = file_get_contents(self::REQUEST_BODY_SOURCE);
                return false !== $requestBody ? $requestBody : null;
            }
        );
    }
}
