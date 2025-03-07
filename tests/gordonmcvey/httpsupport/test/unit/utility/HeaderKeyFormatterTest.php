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

namespace gordonmcvey\httpsupport\test\unit\utility;

use gordonmcvey\httpsupport\utility\HeaderKeyFormatter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HeaderKeyFormatterTest extends TestCase
{
    #[Test]
    public function itFormatsHeadersProperly(): void
    {
        $this->assertSame("Header-With-Hyphens", HeaderKeyFormatter::ucwords("header-with-hyphens"));
        $this->assertSame("Header_with_underscores", HeaderKeyFormatter::ucwords("header_with_underscores"));
    }
}
