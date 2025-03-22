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

namespace gordonmcvey\httpsupport\enum\statusmessages;

enum ServerErrorMessages: string
{
    case INTERNAL_SERVER_ERROR                = "Internal Server Error";
    case NOT_IMPLEMENTED                      = "Not Implemented";
    case BAD_GATEWAY                          = "Bad Gateway";
    case SERVICE_UNAVAILABLE                  = "Service Unavailable";
    case GATEWAY_TIMEOUT                      = "Gateway Timeout";
    case VERSION_NOT_SUPPORTED                = "HTTP Version Not Supported";
    case VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = "Variant Also Negotiates"; // RFC2295
    case INSUFFICIENT_STORAGE                 = "Insufficient Storage"; // RFC4918
    case LOOP_DETECTED                        = "Loop Detected"; // RFC5842
    case NOT_EXTENDED                         = "Not Extended"; // RFC2774
    case NETWORK_AUTHENTICATION_REQUIRED      = "Network Authentication Required"; // RFC6585
}
