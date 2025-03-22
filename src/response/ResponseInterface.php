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

interface ResponseInterface
{
    public function responseCode(): InfoCodes|SuccessCodes|RedirectCodes|ClientErrorCodes|ServerErrorCodes;

    public function setResponseCode(
        InfoCodes|SuccessCodes|RedirectCodes|ClientErrorCodes|ServerErrorCodes $responseCode,
    ): self;

    public function setHeader(string $key, string $value): self;

    public function header(string $key): ?string;

    /**
     * @return array<string, string>
     */
    public function headers(): array;

    public function sendHeaders(): self;

    public function body(): string;

    public function setBody(string $body): self;

    public function contentType(): string;

    public function contentEncoding(): ?string;

    public function contentLength(): int;
}
