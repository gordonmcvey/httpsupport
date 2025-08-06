<?php

declare(strict_types=1);

namespace gordonmcvey\httpsupport\test\unit\request\payload;

use gordonmcvey\httpsupport\request\payload\ArrayPayloadHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ArrayPayloadHandlerTest extends TestCase
{
    #[Test]
    public function itReturnsParam(): void
    {
        $payload = [
            "foo" => "bar",
            "baz" => "quux",
        ];
        $handler = new ArrayPayloadHandler($payload);

        $this->assertSame("bar", $handler->param("foo"));
    }

    #[Test]
    public function itReturnsDefaultValue(): void
    {
        $payload = [
            "foo" => "bar",
            "baz" => "quux",
        ];
        $handler = new ArrayPayloadHandler($payload);

        $this->assertSame("warble", $handler->param("farble", "warble"));
        $this->assertNull($handler->param("farble"));
    }
}
