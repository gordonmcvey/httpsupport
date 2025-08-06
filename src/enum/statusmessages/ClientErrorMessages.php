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

enum ClientErrorMessages: string
{
    case BAD_REQUEST                     = "Bad Request";
    case UNAUTHORIZED                    = "Unauthorized";
    case PAYMENT_REQUIRED                = "Payment Required";
    case FORBIDDEN                       = "Forbidden";
    case NOT_FOUND                       = "Not Found";
    case METHOD_NOT_ALLOWED              = "Method Not Allowed";
    case NOT_ACCEPTABLE                  = "Not Acceptable";
    case PROXY_AUTHENTICATION_REQUIRED   = "Proxy Authentication Required";
    case REQUEST_TIMEOUT                 = "Request Timeout";
    case CONFLICT                        = "Conflict";
    case GONE                            = "Gone";
    case LENGTH_REQUIRED                 = "Length Required";
    case PRECONDITION_FAILED             = "Precondition Failed";
    case REQUEST_ENTITY_TOO_LARGE        = "Content Too Large";
    case REQUEST_URI_TOO_LONG            = "URI Too Long";
    case UNSUPPORTED_MEDIA_TYPE          = "Unsupported Media Type";
    case REQUESTED_RANGE_NOT_SATISFIABLE = "Range Not Satisfiable";
    case EXPECTATION_FAILED              = "Expectation Failed";
    case I_AM_A_TEAPOT                   = "I'm a teapot"; // RFC2324
    case MISDIRECTED_REQUEST             = "Misdirected Request"; // RFC7540
    case UNPROCESSABLE_ENTITY            = "Unprocessable Content"; // RFC4918
    case LOCKED                          = "Locked"; // RFC4918
    case FAILED_DEPENDENCY               = "Failed Dependency"; // RFC4918
    case TOO_EARLY                       = "Too Early"; // RFC-ietf-httpbis-replay-04
    case UPGRADE_REQUIRED                = "Upgrade Required"; // RFC2817
    case PRECONDITION_REQUIRED           = "Precondition Required"; // RFC6585
    case TOO_MANY_REQUESTS               = "Too Many Requests"; // RFC6585
    case REQUEST_HEADER_FIELDS_TOO_LARGE = "Request Header Fields Too Large"; // RFC6585
    case UNAVAILABLE_FOR_LEGAL_REASONS   = "Unavailable For Legal Reasons"; // RFC7725
}
