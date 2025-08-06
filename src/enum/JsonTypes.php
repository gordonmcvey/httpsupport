<?php

declare(strict_types=1);

namespace gordonmcvey\httpsupport\enum;

/**
 * Any JSON media types we want to support should be added here.  For now we're only supporting the most generic
 * application/json media type
 */
enum JsonTypes: string
{
    case JSON = "application/json";
}
