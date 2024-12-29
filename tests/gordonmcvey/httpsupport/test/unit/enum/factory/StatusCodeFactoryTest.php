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

namespace gordonmcvey\httpsupport\test\unit\enum\factory;

use gordonmcvey\httpsupport\enum\factory\StatusCodeFactory;
use gordonmcvey\httpsupport\enum\statuscodes\ClientErrorCodes;
use gordonmcvey\httpsupport\enum\statuscodes\InfoCodes;
use gordonmcvey\httpsupport\enum\statuscodes\RedirectCodes;
use gordonmcvey\httpsupport\enum\statuscodes\ServerErrorCodes;
use gordonmcvey\httpsupport\enum\statuscodes\SuccessCodes;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StatusCodeFactoryTest extends TestCase
{
    #[Test]
    public function itInstantiatesProperEnumsForInts(): void
    {
        $factory = new StatusCodeFactory();
        $this->assertSame(InfoCodes::EARLY_HINTS, $factory->fromInt(InfoCodes::EARLY_HINTS->value));
        $this->assertSame(SuccessCodes::CREATED, $factory->fromInt(SuccessCodes::CREATED->value));
        $this->assertSame(RedirectCodes::FOUND, $factory->fromInt(RedirectCodes::FOUND->value));
        $this->assertSame(ClientErrorCodes::CONFLICT, $factory->fromInt(ClientErrorCodes::CONFLICT->value));
        $this->assertSame(ServerErrorCodes::BAD_GATEWAY, $factory->fromInt(ServerErrorCodes::BAD_GATEWAY->value));
    }

    #[Test]
    public function itInstantiatesProperEnumsForInvalidInts(): void
    {
        $factory = new StatusCodeFactory();
        $this->assertSame(ServerErrorCodes::INTERNAL_SERVER_ERROR, $factory->fromInt(99));
        $this->assertSame(ServerErrorCodes::INTERNAL_SERVER_ERROR, $factory->fromInt(199));
        $this->assertSame(ServerErrorCodes::INTERNAL_SERVER_ERROR, $factory->fromInt(299));
        $this->assertSame(ServerErrorCodes::INTERNAL_SERVER_ERROR, $factory->fromInt(399));
        $this->assertSame(ServerErrorCodes::INTERNAL_SERVER_ERROR, $factory->fromInt(499));
        $this->assertSame(ServerErrorCodes::INTERNAL_SERVER_ERROR, $factory->fromInt(599));
        $this->assertSame(ServerErrorCodes::INTERNAL_SERVER_ERROR, $factory->fromInt(699));
    }

    #[Test]
    public function itInstantiatesProperlyFromErrors(): void
    {
        $factory = new StatusCodeFactory();
        $this->assertSame(
            ClientErrorCodes::CONFLICT,
            $factory->fromThrowable(new \Exception(code: ClientErrorCodes::CONFLICT->value)),
        );
        $this->assertSame(
            ServerErrorCodes::BAD_GATEWAY,
            $factory->fromThrowable(new \Exception(code: ServerErrorCodes::BAD_GATEWAY->value)),
        );
    }

    #[Test]
    public function itInstantiatesProperlyFromErrorsWithOutOfRangeCodes(): void
    {
        $factory = new StatusCodeFactory();
        $this->assertSame(
            ServerErrorCodes::INTERNAL_SERVER_ERROR,
            $factory->fromThrowable(new \Exception(code: 0)),
        );
        $this->assertSame(
            ServerErrorCodes::INTERNAL_SERVER_ERROR,
            $factory->fromThrowable(new \Exception(code: 399)),
        );
        $this->assertSame(
            ServerErrorCodes::INTERNAL_SERVER_ERROR,
            $factory->fromThrowable(new \Exception(code: 600)),
        );
    }
}
