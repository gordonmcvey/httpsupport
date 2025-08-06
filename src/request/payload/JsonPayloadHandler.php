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

namespace gordonmcvey\httpsupport\request\payload;

use gordonmcvey\httpsupport\interface\request\PayloadHandlerInterface;
use JsonException;

class JsonPayloadHandler implements PayloadHandlerInterface
{
    use PayloadReaderTrait;

    /**
     * @var array<array-key, mixed>|null
     */
    private ?array $params = null;

    public function __construct(private readonly string $source = self::REQUEST_BODY_SOURCE)
    {
    }

    /**
     * @throws JsonException
     */
    public function param(string $name, mixed $default = null): mixed
    {
        if (null === $this->params) {
            $payload = $this->body();

            $this->params = null !== $payload ?
                json_decode(json: $payload, associative: true, flags: JSON_THROW_ON_ERROR) :
                [];
        }

        return $this->params[$name] ?? $default;
    }

    public function body(): ?string
    {
        if (null === $this->body) {
            $this->body = $this->readBody($this->source) ?? null;
        }
        return $this->body;
    }
}
