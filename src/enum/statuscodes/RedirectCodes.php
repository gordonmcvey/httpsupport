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

namespace gordonmcvey\httpsupport\enum\statuscodes;

enum RedirectCodes: int
{
    case MULTIPLE_CHOICES     = 300;
    case MOVED_PERMANENTLY    = 301;
    case FOUND                = 302;
    case SEE_OTHER            = 303;
    case NOT_MODIFIED         = 304;
    case USE_PROXY            = 305;
    case RESERVED             = 306; // Was Switch Proxy
    case TEMPORARY_REDIRECT   = 307;
    case PERMANENTLY_REDIRECT = 308; // RFC7238
}
