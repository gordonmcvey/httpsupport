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

 namespace gordonmcvey\httpsupport\test\unit;
 
 use gordonmcvey\httpsupport\enum\statuscodes\SuccessCodes;
 use gordonmcvey\httpsupport\ResponseStatusHeader;
 use PHPUnit\Framework\Attributes\Test;
 use PHPUnit\Framework\TestCase;

 class ResponseStatusHeaderTest extends TestCase
 {
    #[Test]
    public function itGeneratesTheExpectedHeaderString(): void
    {
        $header = new ResponseStatusHeader(1.1, SuccessCodes::ACCEPTED);
        $this->assertEquals("HTTP/1.1 202 Accepted", (string) $header);
    }
 }
