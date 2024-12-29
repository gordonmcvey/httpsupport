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

enum SuccessMessages: string
{
    case OK                            = "OK";
    case CREATED                       = "Created";
    case ACCEPTED                      = "Accepted";
    case NON_AUTHORITATIVE_INFORMATION = "Non-Authoritative Information";
    case NO_CONTENT                    = "No Content";
    case RESET_CONTENT                 = "Reset Content";
    case PARTIAL_CONTENT               = "Partial Content";
    case MULTI_STATUS                  = "Multi-Status"; // RFC4918
    case ALREADY_REPORTED              = "Already Reported"; // RFC5842
    case IM_USED                       = "IM Used"; // RFC3229
}
