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

namespace gordonmcvey\httpsupport\request\psr7;

use gordonmcvey\httpsupport\enum\Verbs;
use gordonmcvey\httpsupport\interface\request\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestAdaptor implements RequestInterface
{
    private ?Verbs $verb = null;

    /**
     * @var array<array-key, mixed>
     */
    private ?array $queryParams = null;

    /**
     * @var array<array-key, mixed>
     */
    private ?array $payloadParams = null;

    /**
     * @var array<array-key, mixed>
     */
    private ?array $cookieParams = null;

    /**
     * @var array<array-key, mixed>
     */
    private ?array $serverParams = null;

    private ?string $body = null;

    public function __construct(private readonly ServerRequestInterface $originalRequest)
    {
    }

    /**
     * @inheritDoc
     */
    public function headers(): array
    {
        return $this->originalRequest->getHeaders();
    }

    public function header(string $name, mixed $default = null): mixed
    {
        return $this->originalRequest->getHeader($name)[0] ?? $default;
    }

    public function setHeader(string $name, mixed $value): self
    {
        $this->originalRequest->withHeader($name, $value);
        return $this;
    }

    public function contentType(): ?string
    {
        return $this->header('Content-Type') ?? null;
    }

    public function contentLength(): ?int
    {
        $contentLength = $this->header('Content-Length');
        return null !== $contentLength ? (int) $contentLength : null;
    }

    public function verb(): Verbs
    {
        if (null === $this->verb) {
            $this->verb = Verbs::from(strtoupper($this->originalRequest->getMethod()));
        }

        return $this->verb;
    }

    public function uri(): string
    {
        return $this->originalRequest->getRequestTarget();
    }

    public function queryParam(string $name, mixed $default = null): mixed
    {
        if (null === $this->queryParams) {
            $this->queryParams = $this->originalRequest->getQueryParams();
        }

        return $this->queryParams[$name] ?? $default;
    }

    public function payloadParam(string $name, mixed $default = null): mixed
    {
        if (null === $this->payloadParams) {
            $this->payloadParams = (array) $this->originalRequest->getParsedBody();
        }

        return $this->payloadParams[$name] ?? $default;
    }

    public function cookieParam(string $name, mixed $default = null): mixed
    {
        if (null === $this->cookieParams) {
            $this->cookieParams = $this->originalRequest->getCookieParams();
        }

        return $this->cookieParams[$name] ?? $default;
    }

    public function serverParam(string $name, mixed $default = null): mixed
    {
        if (null === $this->serverParams) {
            $this->serverParams = $this->originalRequest->getServerParams();
        }

        return $this->serverParams[$name] ?? $default;
    }

    /**
     * @inheritDoc
     * @todo Support uploaded files
     */
    public function uploadedFiles(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     * @todo Support uploaded files
     */
    public function uploadedFile(string $name): ?array
    {
        return null;
    }

    public function body(): ?string
    {
        if (null === $this->body) {
            $this->body = $this->originalRequest->getBody()->getContents();
        }

        return $this->body;
    }
}
