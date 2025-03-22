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

interface RequestInterface
{
    /**
     * @return array<string, mixed>
     */
    public function headers(): array;

    public function header(string $name, mixed $default = null): mixed;

    public function setHeader(string $name, mixed $value): self;

    public function contentType(): ?string;

    public function contentLength(): ?int;

    public function verb(): Verbs;

    public function uri(): string;

    public function param(string $name, mixed $default = null): mixed;

    public function queryParam(string $name, mixed $default = null): mixed;

    public function postParam(string $name, mixed $default = null): mixed;

    public function cookieParam(string $name, mixed $default = null): mixed;

    public function serverParam(string $name, mixed $default = null): mixed;

    /**
     * @return array<string, array{
     *     name: string,
     *     type: string,
     *     size: non-negative-int,
     *     tmp_name: string,
     *     error_code: non-negative-int
     * }>
     */
    public function uploadedFiles(): array;

    /**
     * @return ?array{
     *     name: string,
     *     type: string,
     *     size: non-negative-int,
     *     tmp_name: string,
     *     error_code: non-negative-int
     * }
     */
    public function uploadedFile(string $name): ?array;

    public function body(): ?string;
}
