<?php

declare(strict_types=1);

namespace gordonmcvey\httpsupport\test\unit\request\payload;

use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use JsonException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JsonPayloadHandlerTest extends TestCase
{
    /**
     * @throws JsonException
     */
    #[Test]
    public function itReturnsParam(): void
    {
        $handler = $this->getMockBuilder(JsonPayloadHandler::class)
            ->onlyMethods(["body"])
            ->getMock()
        ;

        $handler->expects($this->once())
            ->method("body")
            ->willReturn(json_encode([
                "foo" => "bar",
                "baz" => "quux",
            ]))
        ;

        $this->assertSame("bar", $handler->param("foo"));
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function itReturnsDefaultValue(): void
    {
        $handler = $this->getMockBuilder(JsonPayloadHandler::class)
            ->onlyMethods(["body"])
            ->getMock()
        ;

        $handler->expects($this->once())
            ->method("body")
            ->willReturn(json_encode([
                "foo" => "bar",
                "baz" => "quux",
            ]))
        ;

        $this->assertSame("warble", $handler->param("farble", "warble"));
        $this->assertNull($handler->param("farble"));
    }
}
