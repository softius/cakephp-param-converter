<?php

namespace ParamConverter\Test\TestCase\Converter;

use Cake\Http\Exception\BadRequestException;
use Cake\TestSuite\TestCase;
use ParamConverter\Converter\FloatConverter;

class FloatConverterTest extends TestCase
{
    public function testSupports(): void
    {
        $converter = new FloatConverter();
        $this->assertTrue($converter->supports('float'));
        $this->assertFalse($converter->supports('int'));
    }

    /**
     * @dataProvider conversionDataProvider
     * @param string $rawValue Raw value
     * @param mixed $expectedValue Expected value upon conversion
     */
    public function testConvertTo(string $rawValue, $expectedValue): void
    {
        $converter = new FloatConverter();
        $convertedValue = $converter->convertTo($rawValue, "float");
        $this->assertEquals($expectedValue, $convertedValue);
        $this->assertIsFloat($convertedValue);
    }

    public function testException(): void
    {
        $converter = new FloatConverter();
        $this->expectException(BadRequestException::class);
        $converter->convertTo("no-float-number", "float");
    }

    /**
     * @return array[]
     */
    public function conversionDataProvider(): array
    {
        return [
            // raw value, converted value
            ['.1', 0.1],
            ['.1E0', 0.1],
            ['.1E-0', 0.1],
            ['1.1', 1.1],
            ['1', 1.0],
            ['01', 1.0],
        ];
    }
}
