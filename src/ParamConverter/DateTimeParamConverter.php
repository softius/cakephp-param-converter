<?php

namespace ParamConverter\ParamConverter;

use Cake\Http\Exception\BadRequestException;
use DateTime;
use Exception;
use ParamConverter\ParamConverterInterface;

/**
 * Class DateTimeParamConverter
 *
 * Param Converter for DateTime class
 *
 * @package ParamConverter
 */
class DateTimeParamConverter implements ParamConverterInterface
{
    /**
     * @inheritDoc
     */
    public function supports(string $class): bool
    {
        return $class === DateTime::class;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function convertTo(string $value, string $class)
    {
        try {
            return new DateTime($value);
        } catch (Exception $e) {
            throw new BadRequestException(null, 0, $e);
        }
    }
}
