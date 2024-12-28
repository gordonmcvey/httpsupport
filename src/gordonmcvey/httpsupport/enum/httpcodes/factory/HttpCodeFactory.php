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

namespace gordonmcvey\httpsupport\enum\httpcodes\factory;

use gordonmcvey\httpsupport\enum\httpcodes\ClientErrorCodes;
use gordonmcvey\httpsupport\enum\httpcodes\InfoCodes;
use gordonmcvey\httpsupport\enum\httpcodes\RedirectCodes;
use gordonmcvey\httpsupport\enum\httpcodes\ServerErrorCodes;
use gordonmcvey\httpsupport\enum\httpcodes\SuccessCodes;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use ValueError;

class HttpCodeFactory implements LoggerAwareInterface
{
    public function __construct(private ?LoggerInterface $logger = null)
    {
    }

    public function fromInt(int $rawCode): InfoCodes|SuccessCodes|RedirectCodes|ClientErrorCodes|ServerErrorCodes
    {
        try {
            return match (true) {
                $rawCode >= 100 && $rawCode <= 199 => InfoCodes::from($rawCode),
                $rawCode >= 200 && $rawCode <= 299 => SuccessCodes::from($rawCode),
                $rawCode >= 300 && $rawCode <= 399 => RedirectCodes::from($rawCode),
                $rawCode >= 400 && $rawCode <= 499 => ClientErrorCodes::from($rawCode),
                $rawCode >= 500 && $rawCode <= 599 => ServerErrorCodes::from($rawCode),
                default => ServerErrorCodes::INTERNAL_SERVER_ERROR,
            };
        } catch (ValueError $e) {
            $this->logger?->error(
                sprintf(
                    "%s: Unable to validate code %s as a valid HTTP code, using default error code",
                    __METHOD__,
                    $rawCode,
                ),
                $e->getTrace(),
            );
            return ServerErrorCodes::INTERNAL_SERVER_ERROR;
        }
    }

    public function fromThrowable(Throwable $e): ClientErrorCodes|ServerErrorCodes
    {
        $throwableCode = $e->getCode();
        if ($throwableCode < 400 || $throwableCode > 599) {
            $this->logger?->notice(sprintf(
                "%s: Error code '%d' not in the range of HTTP error codes, using default",
                $throwableCode,
                __METHOD__,
            ));
            return ServerErrorCodes::INTERNAL_SERVER_ERROR;
        }

        /** @var ClientErrorCodes|ServerErrorCodes $code */
        $code = $this->fromInt($throwableCode);
        return $code;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
