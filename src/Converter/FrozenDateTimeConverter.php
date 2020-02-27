<?php
namespace ParamConverter\Converter;

use Cake\Chronos\Date;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use ParamConverter\ParamConverterInterface;

/**
 * Class FrozenDateTimeConverter
 *
 * Param Converter for FrozenDate and FrozenTime classes
 */
class FrozenDateTimeConverter implements ParamConverterInterface
{
    /**
     * @inheritDoc
     */
    public function supports(string $class): bool
    {
        return $class === FrozenDate::class || $class === FrozenTime::class || $class === Time::class;
    }

    /**
     * @inheritDoc
     *
     * @param string $value from URL.
     * @param string $class FrozenDate or FrozenTime.
     *
     * @return FrozenTime|FrozenDate
     */
    public function convertTo(string $value, string $class)
    {
        try {
            return new $class($value);
        } catch (\Exception $e) {
            throw new BadRequestException(null, 0, $e);
        }
    }
}
