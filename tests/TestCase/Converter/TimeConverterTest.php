<?php

namespace ParamConverter\Test\TestCase\Converter;

use Cake\Http\Exception\BadRequestException;
use Cake\I18n\Time;
use Cake\TestSuite\TestCase;
use ParamConverter\Converter\FrozenDateTimeConverter;

class TimeConverterTest extends TestCase
{
    public function testSupports(): void
    {
        $converter = new FrozenDateTimeConverter();
        $this->assertTrue($converter->supports(Time::class));
    }

    /**
     * @dataProvider conversionDataProvider
     * @param string $rawValue Raw value
     * @param string $expectedValue Expected value upon conversion
     * @param string $format Date format
     */
    public function testConvertTo(string $rawValue, string $expectedValue, string $format): void
    {
        $converter = new FrozenDateTimeConverter();
        /** @var \Cake\I18n\Time $convertedValue */
        $convertedValue = $converter->convertTo($rawValue, Time::class);
        $this->assertInstanceOf(Time::class, $convertedValue);
        $this->assertEquals($expectedValue, $convertedValue->format($format));
    }

    public function testException(): void
    {
        $converter = new FrozenDateTimeConverter();
        $this->expectException(BadRequestException::class);
        $converter->convertTo("notvalid", Time::class);
    }

    /**
     * @return array[]
     */
    public function conversionDataProvider(): array
    {
        return [
            // raw value, converted value
            ['now', date('Y-m-d'), 'Y-m-d'],
            ['now', date('Y-m-d h:i'), 'Y-m-d h:i'],
            ['2020-09-10', '2020-09-10', 'Y-m-d'],
            ['2020-09-10 15:10:00', '2020-09-10 15:10:00', 'Y-m-d H:i:s'],
        ];
    }
}
