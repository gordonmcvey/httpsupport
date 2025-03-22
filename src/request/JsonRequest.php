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

use gordonmcvey\httpsupport\enum\JsonTypes;
use gordonmcvey\httpsupport\enum\statuscodes\ClientErrorCodes;
use ValueError;

class JsonRequest extends Request implements JsonRequestInterface
{
    private mixed $requestBodyJson = null;

    public function __construct(
        array $queryParams,
        array $postParams,
        array $cookieParams,
        array $fileParams,
        array $serverParams,
        mixed $bodyFactory = null
    ) {
        parent::__construct(
            $queryParams,
            $postParams,
            $cookieParams,
            $fileParams,
            $serverParams,
            $bodyFactory,
        );

        try {
            // @phpstan-ignore argument.type
            JsonTypes::from($this->contentType());
        } catch (ValueError $e) {
            throw new ValueError("Content type is not JSON", ClientErrorCodes::BAD_REQUEST->value, $e);
        }
    }

    /**
     * Get the request body as a decoded JSON object
     */
    public function jsonBody(): mixed
    {
        if (null === $this->requestBodyJson) {
            $this->requestBodyJson = json_decode(json: (string) $this->body(), flags: JSON_THROW_ON_ERROR);
        }

        return $this->requestBodyJson;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        // The JSON payload takes precidence over other request values so check there first
        $param = $this->jsonParam($key);
        if (null !== $param) {
            return $param;
        }

        return parent::param($key, $default);
    }

    public function jsonParam(string $key, mixed $default = null): mixed
    {
        $json = $this->jsonBody();
        if (null === $json) {
            return $default;
        }

        return $json->$key ?? $default;
    }
}
