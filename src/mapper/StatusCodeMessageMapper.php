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

namespace gordonmcvey\httpsupport\mapper;

use gordonmcvey\httpsupport\enum\statuscodes\ClientErrorCodes;
use gordonmcvey\httpsupport\enum\statuscodes\InfoCodes;
use gordonmcvey\httpsupport\enum\statuscodes\RedirectCodes;
use gordonmcvey\httpsupport\enum\statuscodes\ServerErrorCodes;
use gordonmcvey\httpsupport\enum\statuscodes\SuccessCodes;
use gordonmcvey\httpsupport\enum\statusmessages\ClientErrorMessages;
use gordonmcvey\httpsupport\enum\statusmessages\InfoMessages;
use gordonmcvey\httpsupport\enum\statusmessages\RedirectMessages;
use gordonmcvey\httpsupport\enum\statusmessages\ServerErrorMessages;
use gordonmcvey\httpsupport\enum\statusmessages\SuccessMessages;

final class StatusCodeMessageMapper
{
    /**
     * @var array<int, InfoMessages|SuccessMessages|RedirectMessages|ClientErrorMessages|ServerErrorMessages>|null
     */
    private static ?array $map = null;

    // Prevent instantiation
    private function __construct()
    {
    }

    public static function forCode(
        InfoCodes|SuccessCodes|RedirectCodes|ClientErrorCodes|ServerErrorCodes $code
    ): InfoMessages|SuccessMessages|RedirectMessages|ClientErrorMessages|ServerErrorMessages {
        return self::getMap()[$code->value];
    }

    /**
     * @return array<int, InfoMessages|SuccessMessages|RedirectMessages|ClientErrorMessages|ServerErrorMessages>
     */
    private static function getMap(): array
    {
        if (null === self::$map) {
            $responseCodes = array_map(fn($code): int => $code->value, array_merge(
                InfoCodes::cases(),
                SuccessCodes::cases(),
                RedirectCodes::cases(),
                ClientErrorCodes::cases(),
                ServerErrorCodes::cases(),
            ));
            $responseMessages = array_merge(
                InfoMessages::cases(),
                SuccessMessages::cases(),
                RedirectMessages::cases(),
                ClientErrorMessages::cases(),
                ServerErrorMessages::cases(),
            );

            // This should never be the case in a production environment.  If it is then I screwed up
            assert(count($responseCodes) === count($responseMessages));

            self::$map = array_combine($responseCodes, $responseMessages);
        }

        return self::$map;
    }
}
