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

enum RedirectMessages: string
{
    case MULTIPLE_CHOICES     = "Multiple Choices";
    case MOVED_PERMANENTLY    = "Moved Permanently";
    case FOUND                = "Found";
    case SEE_OTHER            = "See Other";
    case NOT_MODIFIED         = "Not Modified";
    case USE_PROXY            = "Use Proxy";
    case RESERVED             = "Switch Proxy"; // Was Switch Proxy
    case TEMPORARY_REDIRECT   = "Temporary Redirect";
    case PERMANENTLY_REDIRECT = "Permanent Redirect"; // RFC7238
}
