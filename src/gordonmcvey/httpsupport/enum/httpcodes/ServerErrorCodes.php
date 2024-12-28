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

namespace gordonmcvey\httpsupport\enum\httpcodes;

enum ServerErrorCodes: int
{
    case INTERNAL_SERVER_ERROR                = 500;
    case NOT_IMPLEMENTED                      = 501;
    case BAD_GATEWAY                          = 502;
    case SERVICE_UNAVAILABLE                  = 503;
    case GATEWAY_TIMEOUT                      = 504;
    case VERSION_NOT_SUPPORTED                = 505;
    case VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506; // RFC2295
    case INSUFFICIENT_STORAGE                 = 507; // RFC4918
    case LOOP_DETECTED                        = 508; // RFC5842
    case NOT_EXTENDED                         = 510; // RFC2774
    case NETWORK_AUTHENTICATION_REQUIRED      = 511; // RFC6585
}
