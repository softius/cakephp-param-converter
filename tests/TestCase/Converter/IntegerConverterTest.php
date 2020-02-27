<?php

namespace ParamConverter\Test\TestCase\Converter;

use Cake\Http\Exception\BadRequestException;
use Cake\TestSuite\TestCase;
use ParamConverter\Converter\IntegerConverter;

class IntegerConverterTest extends TestCase
{
    public function testSupports(): void
    {
        $converter = new IntegerConverter();
        $this->assertTrue($converter->supports('int'));
        $this->assertFalse($converter->supports('float'));
    }

    /**
     * @dataProvider conversionDataProvider
     * @param string $rawValue Raw value
     * @param mixed $expectedValue Expected value upon conversion
     */
    public function testConvertTo(string $rawValue, $expectedValue): void
    {
        $converter = new IntegerConverter();
        $convertedValue = $converter->convertTo($rawValue, "int");
        $this->assertEquals($expectedValue, $convertedValue);
        $this->assertIsInt($convertedValue);
    }

    public function testException(): void
    {
        $converter = new IntegerConverter();
        $this->expectException(BadRequestException::class);
        $converter->convertTo("no-int-number", "int");
    }

    /**
     * @return array[]
     */
    public function conversionDataProvider(): array
    {
        return [
            // raw value, converted value
            ['1', 1],
            ['01', 1],
        ];
    }
}
