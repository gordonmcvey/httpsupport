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

namespace gordonmcvey\httpsupport\test\unit\mapper;

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
use gordonmcvey\httpsupport\mapper\StatusCodeMessageMapper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StatusCodeMessageMapperTest extends TestCase
{
    #[Test]
    public function itReturnsCorrectMessagesForStatusCodes(): void
    {
        $this->assertSame(InfoMessages::EARLY_HINTS, StatusCodeMessageMapper::forCode(InfoCodes::EARLY_HINTS));
        $this->assertSame(SuccessMessages::NON_AUTHORITATIVE_INFORMATION, StatusCodeMessageMapper::forCode(SuccessCodes::NON_AUTHORITATIVE_INFORMATION));
        $this->assertSame(RedirectMessages::PERMANENTLY_REDIRECT, StatusCodeMessageMapper::forCode(RedirectCodes::PERMANENTLY_REDIRECT));
        $this->assertSame(ClientErrorMessages::FAILED_DEPENDENCY, StatusCodeMessageMapper::forCode(ClientErrorCodes::FAILED_DEPENDENCY));
        $this->assertSame(ServerErrorMessages::INSUFFICIENT_STORAGE, StatusCodeMessageMapper::forCode(ServerErrorCodes::INSUFFICIENT_STORAGE));
    }
}
